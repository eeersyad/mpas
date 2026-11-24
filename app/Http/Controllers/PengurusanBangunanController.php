<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanBangunanRequest;
use App\Models\Aset;
use App\Models\Bangunan;
use App\Models\DokumenBangunan;
use App\Models\PegawaiAset;
use App\Models\TetapanPenyelenggaraan;
use App\Services\PdfAset;
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
        $mukim = $request->input('mukim');
        $perjanjian = $request->input('perjanjian');

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
            ->with('penyewaAktif:tnt_id,asb_id,tnt_name')
            ->withMax('penyelenggaraan as tarikh_pemeriksaan_terkini', 'asm_inspection_date')
            ->withCount([
                'penyewa as penyewa_aktif_count' => fn ($query) => $query
                    ->where('tnt_status', 'AKTIF'),

                // Penyewa aktif yang perjanjiannya sudah tamat
                'penyewa as perjanjian_tamat_count' => fn ($query) => $query
                    ->where('tnt_status', 'AKTIF')
                    ->whereNotNull('tnt_tamat_perjanjian')
                    ->whereDate('tnt_tamat_perjanjian', '<', today()),

                // Penyewa aktif yang perjanjiannya tamat dalam 3 bulan
                'penyewa as perjanjian_hampir_tamat_count' => fn ($query) => $query
                    ->where('tnt_status', 'AKTIF')
                    ->whereDate('tnt_tamat_perjanjian', '>=', today())
                    ->whereDate('tnt_tamat_perjanjian', '<=', today()->addMonths(3)),
            ])
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asb_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asb_name', 'like', "%{$cari}%")
                        ->orWhere('asb_no_file', 'like', "%{$cari}%")
                        ->orWhere('asb_city', 'like', "%{$cari}%")
                        ->orWhere('asb_category', 'like', "%{$cari}%")
                        ->orWhereHas('penyewaAktif', fn ($q) => $q->where('tnt_name', 'like', "%{$cari}%"));
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
            ->when(
                $mukim,
                fn ($query, $mukim) => $query->where('asb_mukim', $mukim)
            )
            // Penapis perjanjian penyewa: TAMAT atau HAMPIR (dalam 3 bulan)
            ->when($perjanjian === 'TAMAT', fn ($query) => $query->whereHas('penyewa', fn ($q) => $q
                ->where('tnt_status', 'AKTIF')
                ->whereNotNull('tnt_tamat_perjanjian')
                ->whereDate('tnt_tamat_perjanjian', '<', today())))
            ->when($perjanjian === 'HAMPIR', fn ($query) => $query->whereHas('penyewa', fn ($q) => $q
                ->where('tnt_status', 'AKTIF')
                ->whereDate('tnt_tamat_perjanjian', '>=', today())
                ->whereDate('tnt_tamat_perjanjian', '<=', today()->addMonths(3))))
            ->orderBy($kolumSusunan, $arahSusunan)
            ->paginate($had)
            ->withQueryString();

        // Senarai mukim (sama seperti pilihan dalam borang bangunan)
        $senaraiMukim = ['Alor Limbat', 'Bukit Payung', 'Jerong', 'Merchang', 'Pulau Kerengga', 'Rusila'];

        return view('bangunan.senarai', compact('bangunan', 'senaraiMukim'));
    }

    public function create(Pemeriksaan $pemeriksaan): View
    {
        return view('bangunan.daftar', $this->dataBorang($pemeriksaan));
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
                'penyelenggaraan.gambar',
            ]);
        }

        $senaraiPemeriksaan = $bolehLihatPemeriksaan ? $bangunan->penyelenggaraan : collect();

        return view('bangunan.butiran', [
            'bangunan' => $bangunan,
            'bolehLihatPemeriksaan' => $bolehLihatPemeriksaan,
            'pemeriksaanTerkini' => $senaraiPemeriksaan->first(),
            'sejarahPemeriksaan' => $senaraiPemeriksaan->slice(1)->values(),
        ]);
    }

    public function muatTurunPdf(Bangunan $bangunan, PdfAset $pdfAset)
    {
        return response($pdfAset->janaMaklumat('bangunan', $bangunan, TetapanPenyelenggaraan::bolehLihat(auth()->user())), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Aset-Bangunan-' . $this->namaFailSelamat($bangunan->asb_ref_no) . '.pdf"',
        ]);
    }


    /**
     * PDF maklumat pemeriksaan penyelenggaraan (potret) + gambar setiap pemeriksaan.
     */
    public function muatTurunPdfPemeriksaan(Bangunan $bangunan, PdfAset $pdfAset)
    {
        abort_unless(TetapanPenyelenggaraan::bolehLihat(auth()->user()), 403);

        return response($pdfAset->janaPemeriksaan($bangunan), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Pemeriksaan-' . $this->namaFailSelamat($bangunan->asb_ref_no) . '.pdf"',
        ]);
    }

    /**
     * ZIP: maklumat aset (PDF) + semua dokumen asal.
     */
    public function muatTurunZip(Bangunan $bangunan, PdfAset $pdfAset)
    {
        try {
            $laluan = $pdfAset->janaZip('bangunan', $bangunan, TetapanPenyelenggaraan::bolehLihat(auth()->user()));
        } catch (RuntimeException $ralat) {
            return back()->with('ralat', $ralat->getMessage());
        }

        return response()
            ->download($laluan, 'Aset-Bangunan-' . $this->namaFailSelamat($bangunan->asb_ref_no) . '.zip', ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend();
    }
    public function edit(Bangunan $bangunan, Pemeriksaan $pemeriksaan): View
    {
        $bangunan->load([
            'penyewa',
            'dokumen.dimuatNaikOleh',
            'penyelenggaraanTerkini.pemeriksa',
        ]);

        return view('bangunan.kemaskini', [
            'bangunan' => $bangunan,
            ...$this->dataBorang($pemeriksaan, $bangunan),
        ]);
    }

    /**
     * Data yang diperlukan oleh borang daftar dan kemaskini bangunan.
     */
    private function dataBorang(Pemeriksaan $pemeriksaan, ?Bangunan $bangunan = null): array
    {
        $komponenPemeriksaan = $pemeriksaan->komponenAktif();
        $pegawai = PegawaiAset::query()->orderBy('pgw_nama')->get();

        $idPengumpul = old('asb_collector_id', $bangunan?->asb_collector_id);
        $idPengesah = old('asb_verifier_id', $bangunan?->asb_verifier_id);

        // Pegawai aktif, ditambah pegawai yang sudah dipilih (walaupun kini tidak aktif)
        $pilihanPegawai = fn (array $peranan, $idDipilih) => $pegawai
            ->filter(fn ($p) => in_array($p->pgw_peranan, $peranan, true)
                && ($p->aktif() || (string) $p->pgw_id === (string) $idDipilih))
            ->values();

        return [
            'penyewaAktif' => $bangunan?->penyewa->where('tnt_status', 'AKTIF')->values() ?? collect(),

            'komponenPemeriksaan' => $komponenPemeriksaan,
            'jumlahItemPemeriksaan' => $komponenPemeriksaan->sum(fn ($komponen) => count($komponen->itemAktif())),
            'bolehIsiPemeriksaan' => TetapanPenyelenggaraan::bolehIsi(auth()->user()),
            'pemeriksaanTerkini' => $bangunan?->penyelenggaraanTerkini?->load('gambar'),

            'idPengumpul' => $idPengumpul,
            'idPengesah' => $idPengesah,
            'senaraiPengumpul' => $pilihanPegawai(['PENGUMPUL', 'KEDUA_DUA'], $idPengumpul),
            'senaraiPengesah' => $pilihanPegawai(['PENGESAH', 'KEDUA_DUA'], $idPengesah),
            'jawatanPengumpul' => $pegawai->firstWhere('pgw_id', (int) $idPengumpul)?->pgw_jawatan ?? '',
            'jawatanPengesah' => $pegawai->firstWhere('pgw_id', (int) $idPengesah)?->pgw_jawatan ?? '',
        ];
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

    /**
     * Tukar aksara yang tidak dibenarkan dalam nama fail (/ \ : * ? " < > | dan ruang) kepada "-".
     * Contoh: "MDM/PATA/BPH 11/2025(0048)" → "MDM-PATA-BPH-11-2025(0048)"
     */
    private function namaFailSelamat(?string $teks): string
    {
        $bersih = preg_replace('/[\\\\\/:*?"<>|\s]+/', '-', (string) $teks);

        return trim($bersih, '-') ?: 'tanpa-rujukan';
    }

    private function asingkanMaklumat(array $data): array
    {
        $senaraiPenyewa = $data['penyewa'] ?? [];
        $senaraiDokumen = $data['dokumen'] ?? [];
        $dataPemeriksaan = $data['pemeriksaan'] ?? ['mod' => 'TIADA'];

        unset($data['penyewa'], $data['dokumen'], $data['pemeriksaan']);
        
        // Salin nama dan jawatan semasa pegawai ke rekod bangunan
        foreach (['collector', 'verifier'] as $jenis) {
            $pegawai = filled($data["asb_{$jenis}_id"] ?? null)
                ? PegawaiAset::find($data["asb_{$jenis}_id"])
                : null;

            $data["asb_{$jenis}_id"] = $pegawai?->pgw_id;
            $data["asb_{$jenis}_name"] = $pegawai?->pgw_nama;
            $data["asb_{$jenis}_position"] = $pegawai?->pgw_jawatan;
        }

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
                'local'
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

    public function lihatDokumen(Bangunan $bangunan, DokumenBangunan $dokumen)
    {
        abort_unless($dokumen->asb_id === $bangunan->asb_id, 404);

        // Fail baru di disk peribadi; fail lama mungkin masih di disk public
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($dokumen->asbdoc_path)) {
                return Storage::disk($disk)->response(
                    $dokumen->asbdoc_path,
                    $dokumen->asbdoc_name
                );
            }
        }

        abort(404);
    }

    public function hapusDokumen(
        Bangunan $bangunan,
        DokumenBangunan $dokumen
    ): RedirectResponse {
        abort_unless(
            $dokumen->asb_id === $bangunan->asb_id,
            404
        );

        Storage::disk('local')->delete($dokumen->asbdoc_path);
        Storage::disk('public')->delete($dokumen->asbdoc_path);

        $dokumen->delete();

        // Kembali terus ke langkah Dokumen (langkah 8) dalam borang kemaskini
        return redirect()
            ->to(route('bangunan.edit', $bangunan) . '?langkah=8')
            ->with('berjaya', 'Dokumen aset bangunan berjaya dipadam.');
    }
}