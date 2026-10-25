<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\Bangunan;
use App\Models\Tanah;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $ringkasan = match ($user->usr_level) {
            'SYSTEM', 'BPH' => [
                'bangunan' => Bangunan::count(),
                'tanah' => Tanah::count(),
                'aduan' => Aduan::whereIn('cmp_status', [
                    'BARU',
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])->count(),
                'pengguna' => User::where('usr_status', 'ACTIVE')->count(),
            ],

            'BP' => [
                'tanah' => Tanah::count(),
            ],

            'KJ' => [
                'aduan' => Aduan::whereIn('cmp_status', [
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])->count(),

                'diterima' => Aduan::where('cmp_status', 'DITERIMA')->count(),

                'penyelenggaraan' => Aduan::where(
                    'cmp_status',
                    'DALAM_PENYELENGGARAAN'
                )->count(),

                'selesai' => Aduan::where('cmp_status', 'SELESAI')
                    ->whereNotNull('cmp_accepted_by')
                    ->count(),
            ],

            default => [],
        };

        $cartaBangunan = [];
        $cartaTanah = [];
        $skorAset = [];
        $lokasiAset = [];
        $cartaStatusTanah = [];
        $cartaJenisHakmilikTanah = [];
        $cartaAduanKj = [];
        $aduanTindakan = collect();

        if (in_array($user->usr_level, ['SYSTEM', 'BPH'], true)) {
            $jumlahPremis = Bangunan::query()
                ->selectRaw('asb_premise_status, COUNT(*) as jumlah')
                ->groupBy('asb_premise_status')
                ->pluck('jumlah', 'asb_premise_status');

            $cartaBangunan = [
                'label' => [
                    'Sewa',
                    'Tidak Disewa',
                    'Pajak',
                    'Tidak Dipajak',
                    'Kegunaan MDM',
                    'Dilupus',
                ],
                'nilai' => [
                    (int) ($jumlahPremis['SEWA'] ?? 0),
                    (int) ($jumlahPremis['TIDAK_DISEWA'] ?? 0),
                    (int) ($jumlahPremis['PAJAK'] ?? 0),
                    (int) ($jumlahPremis['TIDAK_DIPAJAK'] ?? 0),
                    (int) ($jumlahPremis['KEGUNAAN_MDM'] ?? 0),
                    (int) ($jumlahPremis['DILUPUS'] ?? 0),
                ],
            ];

            $jumlahHakmilik = Tanah::query()
                ->selectRaw('asl_hakmilik_type, COUNT(*) as jumlah')
                ->groupBy('asl_hakmilik_type')
                ->pluck('jumlah', 'asl_hakmilik_type');

            $cartaTanah = [
                'label' => [
                    'Sewa',
                    'Tidak Disewa',
                    'Pajak',
                    'Tidak Dipajak',
                    'Kekal',
                ],
                'nilai' => [
                    (int) ($jumlahHakmilik['SEWA'] ?? 0),
                    (int) ($jumlahHakmilik['TIDAK_DISEWA'] ?? 0),
                    (int) ($jumlahHakmilik['PAJAK'] ?? 0),
                    (int) ($jumlahHakmilik['TIDAK_DIPAJAK'] ?? 0),
                    (int) ($jumlahHakmilik['KEKAL'] ?? 0),
                ],
            ];

            $jumlahSkor = Bangunan::query()
                ->selectRaw('asb_maintenance_status, COUNT(*) as jumlah')
                ->groupBy('asb_maintenance_status')
                ->pluck('jumlah', 'asb_maintenance_status');

            $skorAset = [
                'label' => ['Rendah', 'Sederhana', 'Kritikal'],
                'nilai' => [
                    (int) ($jumlahSkor['RENDAH'] ?? 0),
                    (int) ($jumlahSkor['SEDERHANA'] ?? 0),
                    (int) ($jumlahSkor['KRITIKAL'] ?? 0),
                ],
            ];

            $lokasiBangunan = Bangunan::query()
                ->whereNotNull('asb_latitude')
                ->whereNotNull('asb_longitude')
                ->whereBetween('asb_latitude', [-90, 90])
                ->whereBetween('asb_longitude', [-180, 180])
                ->get([
                    'asb_ref_no',
                    'asb_name',
                    'asb_add1',
                    'asb_latitude',
                    'asb_longitude',
                ])
                ->map(fn ($item) => [
                    'jenis' => 'Bangunan',
                    'rujukan' => $item->asb_ref_no,
                    'nama' => $item->asb_name,
                    'lokasi' => $item->asb_add1,
                    'latitud' => (float) $item->asb_latitude,
                    'longitud' => (float) $item->asb_longitude,
                ]);

            $lokasiTanah = Tanah::query()
                ->whereNotNull('asl_latitude')
                ->whereNotNull('asl_longitude')
                ->whereBetween('asl_latitude', [-90, 90])
                ->whereBetween('asl_longitude', [-180, 180])
                ->get([
                    'asl_ref_no',
                    'asl_lot_no',
                    'asl_location',
                    'asl_latitude',
                    'asl_longitude',
                ])
                ->map(fn ($item) => [
                    'jenis' => 'Tanah',
                    'rujukan' => $item->asl_ref_no,
                    'nama' => $item->asl_lot_no
                        ? 'Lot ' . $item->asl_lot_no
                        : 'Aset Tanah',
                    'lokasi' => $item->asl_location,
                    'latitud' => (float) $item->asl_latitude,
                    'longitud' => (float) $item->asl_longitude,
                ]);

            $lokasiAset = $lokasiBangunan
                ->concat($lokasiTanah)
                ->values();
        }

        if ($user->usr_level === 'BP') {
            $jumlahStatusTanah = Tanah::query()
                ->selectRaw('asl_status_hakmilik, COUNT(*) as jumlah')
                ->groupBy('asl_status_hakmilik')
                ->pluck('jumlah', 'asl_status_hakmilik');

            $cartaStatusTanah = [
                'label' => ['Hakmilik', 'Rizab', 'Strata', 'Lain-lain'],
                'nilai' => [
                    (int) ($jumlahStatusTanah['HAKMILIK'] ?? 0),
                    (int) ($jumlahStatusTanah['RIZAB'] ?? 0),
                    (int) ($jumlahStatusTanah['STRATA'] ?? 0),
                    (int) ($jumlahStatusTanah['LAIN_LAIN'] ?? 0),
                ],
            ];

            $jumlahJenisHakmilik = Tanah::query()
                ->selectRaw('asl_hakmilik_type, COUNT(*) as jumlah')
                ->groupBy('asl_hakmilik_type')
                ->pluck('jumlah', 'asl_hakmilik_type');

            $cartaJenisHakmilikTanah = [
                'label' => [
                    'Sewa',
                    'Tidak Disewa',
                    'Pajak',
                    'Tidak Dipajak',
                    'Kekal',
                ],
                'nilai' => [
                    (int) ($jumlahJenisHakmilik['SEWA'] ?? 0),
                    (int) ($jumlahJenisHakmilik['TIDAK_DISEWA'] ?? 0),
                    (int) ($jumlahJenisHakmilik['PAJAK'] ?? 0),
                    (int) ($jumlahJenisHakmilik['TIDAK_DIPAJAK'] ?? 0),
                    (int) ($jumlahJenisHakmilik['KEKAL'] ?? 0),
                ],
            ];

            $lokasiAset = Tanah::query()
                ->whereNotNull('asl_latitude')
                ->whereNotNull('asl_longitude')
                ->whereBetween('asl_latitude', [-90, 90])
                ->whereBetween('asl_longitude', [-180, 180])
                ->get([
                    'asl_ref_no',
                    'asl_lot_no',
                    'asl_location',
                    'asl_latitude',
                    'asl_longitude',
                ])
                ->map(fn ($item) => [
                    'jenis' => 'Tanah',
                    'rujukan' => $item->asl_ref_no,
                    'nama' => $item->asl_lot_no
                        ? 'Lot ' . $item->asl_lot_no
                        : 'Aset Tanah',
                    'lokasi' => $item->asl_location,
                    'latitud' => (float) $item->asl_latitude,
                    'longitud' => (float) $item->asl_longitude,
                ])
                ->values();
        }

        if ($user->usr_level === 'KJ') {
            $cartaAduanKj = [
                'label' => [
                    'Diterima',
                    'Dalam Penyelenggaraan',
                    'Selesai',
                ],
                'nilai' => [
                    $ringkasan['diterima'],
                    $ringkasan['penyelenggaraan'],
                    $ringkasan['selesai'],
                ],
            ];

            $aduanTindakan = Aduan::query()
                ->with(['bangunan', 'penyewa'])
                ->whereNotNull('cmp_accepted_by')
                ->whereIn('cmp_status', [
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])
                ->orderByRaw("
                    FIELD(
                        cmp_status,
                        'DITERIMA',
                        'DALAM_PENYELENGGARAAN'
                    )
                ")
                ->latest('cmp_received_date')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'ringkasan',
            'cartaBangunan',
            'cartaTanah',
            'skorAset',
            'lokasiAset',
            'cartaStatusTanah',
            'cartaJenisHakmilikTanah',
            'cartaAduanKj',
            'aduanTindakan'
        ));
    }
}