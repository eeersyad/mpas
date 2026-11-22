<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanTanahRequest;
use App\Models\Aset;
use App\Models\DokumenTanah;
use App\Models\Tanah;
use App\Services\PdfAset;
use App\Services\Rujukan;
use App\Support\Jadual;
use App\Support\Susunan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class PengurusanTanahController extends Controller
{
    public function index(Request $request): View
    {
        $had = Jadual::had($request);

        $cari = trim((string) $request->input('cari', ''));
        $statusHakmilik = $request->input('status_hakmilik');
        $statusTanah = $request->input('status_tanah');
        $mukim = $request->input('mukim');

        [$kolumSusunan, $arahSusunan] = Susunan::dapatkan(
            $request,
            [
                'rujukan' => 'asl_ref_no',
                'lot' => 'asl_lot_no',
                'no_fail' => 'asl_no_file',
                'lokasi' => 'asl_location',
                'keluasan' => 'asl_land_size',
                'status_hakmilik' => 'asl_status_hakmilik',
                'status_tanah' => 'asl_status_tanah',
                'terkini' => 'asl_id',
            ],
            'terkini'
        );

        $tanah = Tanah::query()
            ->with('aset')
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asl_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asl_lot_no', 'like', "%{$cari}%")
                        ->orWhere('asl_no_file', 'like', "%{$cari}%")
                        ->orWhere('asl_location', 'like', "%{$cari}%")
                        ->orWhere('asl_hakmilik_no', 'like', "%{$cari}%")
                        ->orWhere('asl_status_tanah', 'like', "%{$cari}%");
                });
            })
            ->when(
                $statusHakmilik,
                fn ($query, $status) => $query->where('asl_status_hakmilik', $status)
            )
            ->when(
                $statusTanah,
                fn ($query, $status) => $query->where('asl_status_tanah', $status)
            )
            ->when(
                $mukim,
                fn ($query, $mukim) => $query->where('asl_mukim', $mukim)
            )
            ->orderBy($kolumSusunan, $arahSusunan)
            ->paginate($had)
            ->withQueryString();

        // Senarai mukim: [kod => label] (sama seperti pilihan dalam borang tanah)
        $senaraiMukim = collect(Tanah::SENARAI_MUKIM)
            ->mapWithKeys(fn ($kod) => [$kod => \App\Support\Paparan::label($kod)])
            ->all();

        return view('tanah.senarai', compact('tanah', 'senaraiMukim'));
    }

    public function create(): View
    {
        return view('tanah.daftar');
    }

    public function store(
        SimpanTanahRequest $request,
        Rujukan $rujukan
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $rujukan): void {
                [$maklumatTanah, $senaraiDokumen] = $this->asingkanMaklumat(
                    $request->validated()
                );

                $aset = Aset::create([
                    'ast_name' => 'TANAH',
                    'created_by' => $request->user()->usr_id,
                ]);

                $tanah = Tanah::create([
                    ...$maklumatTanah,
                    'ast_id' => $aset->ast_id,
                    'asl_ref_no' => $rujukan->jana(
                        'TANAH',
                        $request->user()->usr_id
                    ),
                ]);

                $this->sahkanHadDokumen($tanah, $senaraiDokumen);

                $this->simpanDokumen(
                    $tanah,
                    $senaraiDokumen,
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
            ->route('tanah.index')
            ->with('berjaya', 'Aset tanah berjaya didaftarkan.');
    }

    public function butiran(Tanah $tanah): View
    {
        $tanah->load([
            'aset',
            'dokumen.dimuatNaikOleh',
        ]);

        return view('tanah.butiran', compact('tanah'));
    }

    public function muatTurunPdf(Tanah $tanah, PdfAset $pdfAset)
    {
        return response($pdfAset->janaMaklumat('tanah', $tanah), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Aset-Tanah-' . $this->namaFailSelamat($tanah->asl_ref_no) . '.pdf"',
        ]);
    }


    /**
     * ZIP: maklumat aset (PDF) + semua dokumen asal.
     */
    public function muatTurunZip(Tanah $tanah, PdfAset $pdfAset)
    {
        try {
            $laluan = $pdfAset->janaZip('tanah', $tanah);
        } catch (RuntimeException $ralat) {
            return back()->with('ralat', $ralat->getMessage());
        }

        return response()
            ->download($laluan, 'Aset-Tanah-' . $this->namaFailSelamat($tanah->asl_ref_no) . '.zip', ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend();
    }
    public function edit(Tanah $tanah): View
    {
        $tanah->load('dokumen.dimuatNaikOleh');

        return view('tanah.kemaskini', compact('tanah'));
    }

    public function update(
        SimpanTanahRequest $request,
        Tanah $tanah
    ): RedirectResponse {
        DB::transaction(function () use ($request, $tanah): void {
            [$maklumatTanah, $senaraiDokumen] = $this->asingkanMaklumat(
                $request->validated()
            );

            $this->sahkanHadDokumen($tanah, $senaraiDokumen);

            $tanah->update($maklumatTanah);

            $tanah->aset()->update([
                'updated_by' => $request->user()->usr_id,
            ]);

            $this->simpanDokumen(
                $tanah,
                $senaraiDokumen,
                $request->user()->usr_id
            );
        });

        return redirect()
            ->route('tanah.index')
            ->with('berjaya', 'Maklumat aset tanah berjaya dikemaskini.');
    }

    public function lihatDokumen(Tanah $tanah, DokumenTanah $dokumen)
    {
        abort_unless($dokumen->asl_id === $tanah->asl_id, 404);

        // Fail baru di disk peribadi; fail lama mungkin masih di disk public
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($dokumen->asldoc_path)) {
                return Storage::disk($disk)->response(
                    $dokumen->asldoc_path,
                    $dokumen->asldoc_name
                );
            }
        }

        abort(404);
    }

    public function hapusDokumen(
        Tanah $tanah,
        DokumenTanah $dokumen
    ): RedirectResponse {
        abort_unless(
            $dokumen->asl_id === $tanah->asl_id,
            404
        );

        Storage::disk('local')->delete($dokumen->asldoc_path);
        Storage::disk('public')->delete($dokumen->asldoc_path);

        $dokumen->delete();

        // Kembali terus ke langkah Dokumen (langkah 5) dalam borang kemaskini
        return redirect()
            ->to(route('tanah.edit', $tanah) . '?langkah=5')
            ->with('berjaya', 'Dokumen aset tanah berjaya dipadam.');
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
        $senaraiDokumen = $data['dokumen'] ?? [];

        unset($data['dokumen']);

        return [$data, $senaraiDokumen];
    }

    private function sahkanHadDokumen(
        Tanah $tanah,
        array $senaraiDokumen
    ): void {
        if ($tanah->dokumen()->count() + count($senaraiDokumen) > 4) {
            throw ValidationException::withMessages([
                'dokumen' => 'Setiap aset tanah hanya boleh mempunyai maksimum 4 dokumen.',
            ]);
        }
    }

    private function simpanDokumen(
        Tanah $tanah,
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
                'dokumen/tanah/' . $tanah->asl_id,
                'local'
            );

            $tanah->dokumen()->create([
                'asldoc_type' => $jenis,
                'asldoc_name' => Str::limit(
                    $fail->getClientOriginalName(),
                    255,
                    ''
                ),
                'asldoc_path' => $path,
                'uploaded_by' => $userId,
                'uploaded_at' => now(),
            ]);
        }
    }
}