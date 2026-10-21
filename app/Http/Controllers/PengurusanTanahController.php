<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanTanahRequest;
use App\Models\Aset;
use App\Models\DokumenTanah;
use App\Models\Tanah;
use App\Services\Rujukan;
use App\Support\Jadual;
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
        $jenisHakmilik = $request->input('jenis_hakmilik');

        $tanah = Tanah::query()
            ->with('aset')
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asl_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asl_lot_no', 'like', "%{$cari}%")
                        ->orWhere('asl_location', 'like', "%{$cari}%")
                        ->orWhere('asl_hakmilik_no', 'like', "%{$cari}%");
                });
            })
            ->when(
                $statusHakmilik,
                fn ($query, $status) => $query->where('asl_status_hakmilik', $status)
            )
            ->when(
                $jenisHakmilik,
                fn ($query, $jenis) => $query->where('asl_hakmilik_type', $jenis)
            )
            ->latest('asl_id')
            ->paginate($had)
            ->withQueryString();

        return view('tanah.senarai', compact('tanah'));
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

    public function hapusDokumen(
        Tanah $tanah,
        DokumenTanah $dokumen
    ): RedirectResponse {
        abort_unless(
            $dokumen->asl_id === $tanah->asl_id,
            404
        );

        Storage::disk('public')->delete($dokumen->asldoc_path);

        $dokumen->delete();

        return back()->with(
            'berjaya',
            'Dokumen aset tanah berjaya dipadam.'
        );
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
                'public'
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