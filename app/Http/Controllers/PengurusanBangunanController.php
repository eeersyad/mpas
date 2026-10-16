<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanBangunanRequest;
use App\Models\Aset;
use App\Models\Bangunan;
use App\Services\Rujukan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class PengurusanBangunanController extends Controller
{
    public function index(): View
    {
        $bangunan = Bangunan::query()
            ->with('penyewa')
            ->latest('asb_id')
            ->paginate(10);

        return view('bangunan.senarai', compact('bangunan'));
    }

    public function create(): View
    {
        return view('bangunan.daftar');
    }

    public function store(
        SimpanBangunanRequest $request,
        Rujukan $rujukan
    ): RedirectResponse {
        try {
            DB::transaction(function () use ($request, $rujukan): void {
                [$maklumatBangunan, $senaraiPenyewa] = $this->asingkanMaklumat(
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

                $this->simpanPenyewa(
                    $bangunan,
                    $senaraiPenyewa,
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

    public function edit(Bangunan $bangunan): View
    {
        $bangunan->load('penyewa');

        return view('bangunan.kemaskini', compact('bangunan'));
    }

    public function update(
        SimpanBangunanRequest $request,
        Bangunan $bangunan
    ): RedirectResponse {
        DB::transaction(function () use ($request, $bangunan): void {
            [$maklumatBangunan, $senaraiPenyewa] = $this->asingkanMaklumat(
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
        });

        return redirect()
            ->route('bangunan.index')
            ->with('berjaya', 'Maklumat aset bangunan berjaya dikemaskini.');
    }

    private function asingkanMaklumat(array $data): array
    {
        $senaraiPenyewa = $data['penyewa'] ?? [];

        unset($data['penyewa']);

        $kosAsal = $data['asb_kos_siap_bina_asal'] ?? null;
        $kosTambahan = $data['asb_kos_tambahan'] ?? null;

        $data['asb_total_kos'] = $kosAsal !== null || $kosTambahan !== null
            ? (float) ($kosAsal ?? 0) + (float) ($kosTambahan ?? 0)
            : null;

        $data['asb_total_block'] = $data['asb_total_block'] ?? 0;
        $data['asb_total_ext_structure'] = $data['asb_total_ext_structure'] ?? 0;

        return [$data, $senaraiPenyewa];
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
}