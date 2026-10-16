<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanAduanAwamRequest;
use App\Models\Aduan;
use App\Models\KodBangunan;
use App\Models\TetapanAduan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AduanAwamController extends Controller
{
    public function create(string $kod): View
    {
        $kodBangunan = KodBangunan::query()
            ->with([
                'bangunan.penyewa' => fn ($query) => $query
                    ->where('tnt_status', 'AKTIF')
                    ->orderBy('tnt_name'),
            ])
            ->where('qr_code_value', $kod)
            ->firstOrFail();

        if ($kodBangunan->qr_status !== 'ACTIVE') {
            return view('aduan.tidakaktif', compact('kodBangunan'));
        }

        $tempohHari = TetapanAduan::query()
            ->value('adu_tempoh_papar_selesai') ?? 30;

        $tarikhHad = now()->subDays($tempohHari);

        $progressAduan = Aduan::query()
            ->where('asb_id', $kodBangunan->asb_id)
            ->where(function ($query) use ($tarikhHad) {
                $query->whereIn('cmp_status', [
                    'BARU',
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])->orWhere(function ($query) use ($tarikhHad) {
                    $query->where('cmp_status', 'SELESAI')
                        ->whereNotNull('cmp_accepted_by')
                        ->where('cmp_completed_at', '>=', $tarikhHad);
                });
            })
            ->latest('cmp_received_date')
            ->get();

        return view('aduan.awam', compact(
            'kodBangunan',
            'progressAduan',
            'tempohHari'
        ));
    }

    public function store(
        SimpanAduanAwamRequest $request,
        string $kod
    ): RedirectResponse {
        $kodBangunan = KodBangunan::query()
            ->with('bangunan')
            ->where('qr_code_value', $kod)
            ->where('qr_status', 'ACTIVE')
            ->firstOrFail();

        $data = $request->validated();

        $tenantId = null;

        if ($data['jenis_pengadu'] === 'PENYEWA') {
            $tenantId = $kodBangunan->bangunan
                ->penyewa()
                ->where('tnt_id', $data['tnt_id'])
                ->where('tnt_status', 'AKTIF')
                ->value('tnt_id');

            if (! $tenantId) {
                throw ValidationException::withMessages([
                    'tnt_id' => 'Penyewa yang dipilih tidak sah untuk aset ini.',
                ]);
            }
        }

        DB::transaction(function () use (
            $request,
            $kodBangunan,
            $data,
            $tenantId
        ): void {
            $aduan = Aduan::create([
                'asb_id' => $kodBangunan->asb_id,
                'tnt_id' => $tenantId,
                'cmp_received_date' => now(),
                'cmp_note' => $data['cmp_note'],
                'cmp_damage_type' => $data['cmp_damage_type'],
                'cmp_status' => 'BARU',
            ]);

            $kedudukanGambar = [
                'KIRI',
                'KANAN',
                'DEPAN',
                'BELAKANG',
            ];

            foreach ($request->file('gambar', []) as $indeks => $gambar) {
                $laluan = $gambar->store(
                    'aduan/' . $aduan->cmp_id,
                    'public'
                );

                $aduan->gambar()->create([
                    'cmpimg_position' => $kedudukanGambar[$indeks],
                    'cmpimg_name' => $gambar->getClientOriginalName(),
                    'cmpimg_path' => $laluan,
                ]);
            }
        });

        return redirect()
            ->route('aduan.awam.create', $kod)
            ->with('berjaya', 'Aduan anda telah berjaya dihantar untuk tindakan BPH.');
    }
}