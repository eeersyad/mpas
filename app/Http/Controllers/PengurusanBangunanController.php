<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanBangunanRequest;
use App\Models\Aset;
use App\Models\Bangunan;
use App\Models\DokumenBangunan;
use App\Models\TetapanPenyelenggaraan;
use App\Services\Rujukan;
use App\Services\KodQr;
use App\Services\Pemeriksaan;
use App\Support\Jadual;
use App\Support\Susunan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class PengurusanBangunanController extends Controller
{
    public function index(Request $request): View
    {
        $had = Jadual::had($request);

        $cari = trim((string) $request->input('cari', ''));
        $statusPremis = $request->input('status_premis');
        $statusPenyelenggaraan = $request->input('status_penyelenggaraan');

        [$kolumSusunan, $arahSusunan] = Susunan::dapatkan(
            $request,
            [
                'rujukan' => 'asb_ref_no',
                'nama' => 'asb_name',
                'no_fail' => 'asb_no_file',
                'kategori' => 'asb_category',
                'status_premis' => 'asb_premise_status',
                'penyelenggaraan' => 'asb_maintenance_status',
                'penyewa' => 'penyewa_aktif_count',
                'terkini' => 'asb_id',
            ],
            'terkini'
        );

        $bangunan = Bangunan::query()
            ->withCount([
                'penyewa as penyewa_aktif_count' => fn ($query) => $query
                    ->where('tnt_status', 'AKTIF'),
            ])
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asb_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asb_name', 'like', "%{$cari}%")
                        ->orWhere('asb_no_file', 'like', "%{$cari}%")
                        ->orWhere('asb_city', 'like', "%{$cari}%")
                        ->orWhere('asb_category', 'like', "%{$cari}%");
                });
            })
            ->when(
                $statusPremis,
                fn ($query, $status) => $query->where('asb_premise_status', $status)
            )
            ->when(
                $statusPenyelenggaraan,
                fn ($query, $status) => $query->where('asb_maintenance_status', $status)
            )
            ->orderBy($kolumSusunan, $arahSusunan)
            ->paginate($had)
            ->withQueryString();

        return view('bangunan.senarai', compact('bangunan'));
    }

    public function create(): View
    {
        return view('bangunan.daftar');
    }

    public function store(
        SimpanBangunanRequest $request,
        Rujukan $rujukan,
        KodQr $kodQr,
        Pemeriksaan $pemeriksaan
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $rujukan, $kodQr, $pemeriksaan): void {
                [$maklumatBangunan, $senaraiPenyewa, $senaraiDokumen, $dataPemeriksaan] = $this->asingkanMaklumat(
                    $request->validated()
                );

                $aset = Aset::create([
                    'ast_name' => 'BANGUNAN',
                    'created_by' => $request->user()->usr_id,
                ]);

                $bangunan = Bangunan::create([
                    ...$maklumatBangunan,
                    'ast_id' => $aset->ast_id,
                    'asb_ref_no' => $rujukan->jana(
                        'BANGUNAN',
                        $request->user()->usr_id
                    ),
                    'created_by' => $request->user()->usr_id,
                ]);

                $kodQr->jana(
                    $bangunan,
                    $request->user()->usr_id
                );

                $this->simpanPenyewa(
                    $bangunan,
                    $senaraiPenyewa,
                    $request->user()->usr_id
                );

                $this->simpanDokumen(
                    $bangunan,
                    $senaraiDokumen,
                    $request->user()->usr_id
                );

                $pemeriksaan->simpan(
                    $bangunan,
                    $dataPemeriksaan,
                    $request->user()->usr_id
                );
            });
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'tetapan' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('bangunan.index')
            ->with('berjaya', 'Aset bangunan berjaya didaftarkan.');
    }

    public function butiran(Request $request, Bangunan $bangunan): View|Response
    {
        $user = $request->user();

        $pengurusAset = $user->mempunyaiPeranan(['SYSTEM', 'BPH']);
        $bolehLihatPemeriksaan = TetapanPenyelenggaraan::bolehLihat($user);

        // Pilihan B: peranan lain hanya boleh buka jika ada akses lihat penyelenggaraan
        if (! $pengurusAset && ! $bolehLihatPemeriksaan) {
            return response()->view('ralat.terhad', [], 403);
        }

        $bangunan->load([
            'penyewa',
            'kod',
            'dokumen.dimuatNaikOleh',
            'aduan' => fn ($query) => $query->latest('cmp_received_date')->limit(5),
        ]);

        if ($bolehLihatPemeriksaan) {
            $bangunan->load([
                'penyelenggaraan.pemeriksa',
                'penyelenggaraan.dikemaskiniOleh',
            ]);
        }

        return view('bangunan.butiran', compact('bangunan', 'bolehLihatPemeriksaan'));
    }

    public function muatTurunPdf(Bangunan $bangunan)
    {
        $bangunan->load('penyewa');

        $pdf = Pdf::loadView('pdf.aset-butiran', [
            'jenis' => 'bangunan',
            'aset' => $bangunan,
            'tarikh' => now(),
            'logoMdm' => base64_encode(
                file_get_contents(public_path('images/logo-mdm.png'))
            ),
            'logoMpas' => base64_encode(
                file_get_contents(public_path('images/logo-mpas-2.png'))
            ),
        ])
            ->setPaper('a4', 'landscape');

        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $kanvas = $dompdf->getCanvas();

        $font = $dompdf->getFontMetrics()->getFont(
            'DejaVu Sans',
            'normal'
        );

        $kanvas->page_text(
            24,
            570,
            'Dokumen ini dijana oleh MPAS untuk kegunaan rasmi Majlis Daerah Marang.',
            $font,
            7,
            [0.33, 0.33, 0.33]
        );

        $kanvas->page_text(
            742,
            570,
            'Halaman {PAGE_NUM} / {PAGE_COUNT}',
            $font,
            7,
            [0.33, 0.33, 0.33]
        );

        return $pdf->download(
            "Aset-Bangunan-{$bangunan->asb_ref_no}.pdf"
        );
    }

    public function edit(Bangunan $bangunan): View
    {
        $bangunan->load([
            'penyewa',
            'dokumen.dimuatNaikOleh',
            'penyelenggaraanTerkini.pemeriksa',
        ]);

        return view('bangunan.kemaskini', compact('bangunan'));
    }

    public function update(
        SimpanBangunanRequest $request,
        Bangunan $bangunan,
        Pemeriksaan $pemeriksaan
    ): RedirectResponse {
        DB::transaction(function () use ($request, $bangunan, $pemeriksaan): void {
            [$maklumatBangunan, $senaraiPenyewa, $senaraiDokumen, $dataPemeriksaan] = $this->asingkanMaklumat(
                $request->validated()
            );

            $bangunan->update([
                ...$maklumatBangunan,
                'updated_by' => $request->user()->usr_id,
            ]);

            $bangunan->aset()->update([
                'updated_by' => $request->user()->usr_id,
            ]);

            $this->simpanPenyewa(
                $bangunan,
                $senaraiPenyewa,
                $request->user()->usr_id
            );

            $this->simpanDokumen(
                $bangunan,
                $senaraiDokumen,
                $request->user()->usr_id
            );

            $pemeriksaan->simpan(
                $bangunan,
                $dataPemeriksaan,
                $request->user()->usr_id
            );
        });

        return redirect()
            ->route('bangunan.index')
            ->with('berjaya', 'Maklumat aset bangunan berjaya dikemaskini.');
    }

    private function asingkanMaklumat(array $data): array
    {
        $senaraiPenyewa = $data['penyewa'] ?? [];
        $senaraiDokumen = $data['dokumen'] ?? [];
        $dataPemeriksaan = $data['pemeriksaan'] ?? ['mod' => 'TIADA'];

        unset($data['penyewa'], $data['dokumen'], $data['pemeriksaan']);
        
        $data['asb_distinct'] = 'Marang';
        $data['asb_state'] = 'Terengganu';

        $kosAsal = $data['asb_kos_siap_bina_asal'] ?? null;
        $kosTambahan = $data['asb_kos_tambahan'] ?? null;

        $data['asb_total_kos'] = $kosAsal !== null || $kosTambahan !== null ? (float) ($kosAsal ?? 0) + (float) ($kosTambahan ?? 0) : null;
        $data['asb_total_block'] = $data['asb_total_block'] ?? 0;
        $data['asb_total_ext_structure'] = $data['asb_total_ext_structure'] ?? 0;

        return [$data, $senaraiPenyewa, $senaraiDokumen, $dataPemeriksaan];
    }

    private function simpanPenyewa(
        Bangunan $bangunan,
        array $senaraiPenyewa,
        int $userId
    ): void {
        $idPenyewaAktif = [];

        foreach ($senaraiPenyewa as $maklumat) {
            if (empty($maklumat['tnt_name'])) {
                continue;
            }

            $idPenyewa = $maklumat['tnt_id'] ?? null;

            unset($maklumat['tnt_id']);

            $maklumat['tnt_status'] = $maklumat['tnt_status'] ?? 'AKTIF';

            if ($idPenyewa) {
                $penyewa = $bangunan->penyewa()
                    ->where('tnt_id', $idPenyewa)
                    ->firstOrFail();

                $penyewa->update([
                    ...$maklumat,
                    'updated_by' => $userId,
                ]);

                $idPenyewaAktif[] = $penyewa->tnt_id;

                continue;
            }

            $penyewa = $bangunan->penyewa()->create([
                ...$maklumat,
                'created_by' => $userId,
            ]);

            $idPenyewaAktif[] = $penyewa->tnt_id;
        }

        if ($bangunan->exists) {
            $query = $bangunan->penyewa();

            if (! empty($idPenyewaAktif)) {
                $query->whereNotIn('tnt_id', $idPenyewaAktif);
            }

            $query->update([
                'tnt_status' => 'TIDAK_AKTIF',
                'updated_by' => $userId,
            ]);
        }
    }

    private function simpanDokumen(
        Bangunan $bangunan,
        array $senaraiDokumen,
        int $userId
    ): void {
        foreach ($senaraiDokumen as $maklumat) {
            $fail = $maklumat['fail'] ?? null;
            $jenis = $maklumat['jenis'] ?? null;

            if (! $fail || ! $jenis) {
                continue;
            }

            $path = $fail->store(
                'dokumen/bangunan/' . $bangunan->asb_id,
                'public'
            );

            $bangunan->dokumen()->create([
                'asbdoc_type' => $jenis,
                'asbdoc_name' => Str::limit(
                    $fail->getClientOriginalName(),
                    255,
                    ''
                ),
                'asbdoc_path' => $path,
                'uploaded_by' => $userId,
                'uploaded_at' => now(),
            ]);
        }
    }

    public function hapusDokumen(
        Bangunan $bangunan,
        DokumenBangunan $dokumen
    ): RedirectResponse {
        abort_unless(
            $dokumen->asb_id === $bangunan->asb_id,
            404
        );

        Storage::disk('public')->delete($dokumen->asbdoc_path);

        $dokumen->delete();

        return back()->with(
            'berjaya',
            'Dokumen aset bangunan berjaya dipadam.'
        );
    }
}