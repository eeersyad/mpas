<?php

namespace App\Http\Controllers;

use App\Exports\EksportLaporan;
use App\Models\Aduan;
use App\Models\Bangunan;
use App\Models\Tanah;
use App\Support\Jadual;
use App\Support\Paparan;
use App\Support\Susunan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PengurusanLaporanController extends Controller
{
    public function index(): View
    {
        $laporan = $this->senaraiLaporan(request()->user()->usr_level);

        return view('laporan.utama', compact('laporan'));
    }

    public function papar(Request $request, string $jenis): View
    {
        $konfigurasi = $this->sahkanAkses($request, $jenis);
        $had = Jadual::had($request);

        $rekod = $this->pembinaRekod($request, $jenis)
            ->paginate($had)
            ->withQueryString();

        return view('laporan.paparan', compact(
            'jenis',
            'konfigurasi',
            'rekod'
        ));
    }

    public function excel(Request $request, string $jenis)
    {
        $konfigurasi = $this->sahkanAkses($request, $jenis);

        $data = $this->dataEksport(
            $jenis,
            $this->pembinaRekod($request, $jenis)->get()
        );

        return Excel::download(
            new EksportLaporan(
                $data['kepala'],
                $data['baris'],
                $konfigurasi['tajuk']
            ),
            $this->namaFail($jenis, 'xlsx')
        );
    }

    public function pdf(Request $request, string $jenis)
    {
        $konfigurasi = $this->sahkanAkses($request, $jenis);

        $data = $this->dataEksport(
            $jenis,
            $this->pembinaRekod($request, $jenis)->get()
        );

        $pdf = Pdf::loadView('laporan.pdf', [
            'tajuk' => $konfigurasi['tajuk'],
            'kepala' => $data['kepala'],
            'baris' => $data['baris'],
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
            22,
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

        return $pdf->download($this->namaFail($jenis, 'pdf'));
    }

    private function pembinaRekod(Request $request, string $jenis)
    {
        return match ($jenis) {
            'bangunan' => $this->queryBangunan($request),
            'tanah' => $this->queryTanah($request),
            'aduan' => $this->queryAduan($request, false),
            'penyelenggaraan' => $this->queryAduan($request, true),
        };
    }

    private function queryBangunan(Request $request)
    {
        $cari = trim((string) $request->input('cari', ''));

        [$kolumSusunan, $arahSusunan] = Susunan::dapatkan(
            $request,
            [
                'rujukan' => 'asb_ref_no',
                'nama' => 'asb_name',
                'status_premis' => 'asb_premise_status',
                'penyelenggaraan' => 'asb_maintenance_status',
                'penyewa' => 'penyewa_aktif_count',
                'terkini' => 'asb_id',
            ],
            'terkini'
        );

        return Bangunan::query()
            ->withCount([
                'penyewa as penyewa_aktif_count' => fn ($query) => $query
                    ->where('tnt_status', 'AKTIF'),
            ])
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asb_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asb_name', 'like', "%{$cari}%")
                        ->orWhere('asb_city', 'like', "%{$cari}%");
                });
            })
            ->when(
                $request->filled('status_premis'),
                fn ($query) => $query->where(
                    'asb_premise_status',
                    $request->string('status_premis')->toString()
                )
            )
            ->when(
                $request->filled('status_penyelenggaraan'),
                fn ($query) => $query->where(
                    'asb_maintenance_status',
                    $request->string('status_penyelenggaraan')->toString()
                )
            )
            ->orderBy($kolumSusunan, $arahSusunan);
    }

    private function queryTanah(Request $request)
    {
        $cari = trim((string) $request->input('cari', ''));

        [$kolumSusunan, $arahSusunan] = Susunan::dapatkan(
            $request,
            [
                'rujukan' => 'asl_ref_no',
                'lot' => 'asl_lot_no',
                'lokasi' => 'asl_location',
                'status_hakmilik' => 'asl_status_hakmilik',
                'status_tanah' => 'asl_status_tanah',
                'keluasan' => 'asl_land_size',
                'terkini' => 'asl_id',
            ],
            'terkini'
        );

        return Tanah::query()
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asl_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asl_lot_no', 'like', "%{$cari}%")
                        ->orWhere('asl_location', 'like', "%{$cari}%")
                        ->orWhere('asl_hakmilik_no', 'like', "%{$cari}%")
                        ->orWhere('asl_status_tanah', 'like', "%{$cari}%");
                });
            })
            ->when(
                $request->filled('status_hakmilik'),
                fn ($query) => $query->where(
                    'asl_status_hakmilik',
                    $request->string('status_hakmilik')->toString()
                )
            )
            ->when(
                $request->filled('status_tanah'),
                fn ($query) => $query->where(
                    'asl_status_tanah',
                    $request->string('status_tanah')->toString()
                )
            )
            ->orderBy($kolumSusunan, $arahSusunan);
    }

    private function queryAduan(Request $request, bool $penyelenggaraan)
    {
        $cari = trim((string) $request->input('cari', ''));

        [$kolumSusunan, $arahSusunan] = Susunan::dapatkan(
            $request,
            [
                'tarikh' => 'cmp_received_date',
                'aset' => 'aset_bangunan',
                'kerosakan' => 'cmp_damage_type',
                'pengadu' => 'nama_pengadu',
                'status' => 'cmp_status',
                'terkini' => 'cmp_received_date',
            ],
            'terkini'
        );

        return Aduan::query()
            ->with(['bangunan', 'penyewa'])
            ->when($penyelenggaraan, function ($query): void {
                $query->whereNotNull('cmp_accepted_by')
                    ->whereIn('cmp_status', [
                        'DITERIMA',
                        'DALAM_PENYELENGGARAAN',
                        'SELESAI',
                    ]);
            })
            ->when(
                $request->user()->usr_level === 'KJ',
                fn ($query) => $query->whereNotNull('cmp_accepted_by')
            )
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('cmp_damage_type', 'like', "%{$cari}%")
                        ->orWhere('cmp_note', 'like', "%{$cari}%")
                        ->orWhereHas('bangunan', function ($query) use ($cari): void {
                            $query->where('asb_name', 'like', "%{$cari}%")
                                ->orWhere('asb_ref_no', 'like', "%{$cari}%");
                        });
                });
            })
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'cmp_status',
                    $request->string('status')->toString()
                )
            )
            ->when(
                $request->input('jenis_pengadu') === 'PENYEWA',
                fn ($query) => $query->whereNotNull('tnt_id')
            )
            ->when(
                $request->input('jenis_pengadu') === 'AWAM',
                fn ($query) => $query->whereNull('tnt_id')
            )
            ->when(
                $request->filled('tarikh_mula'),
                fn ($query) => $query->whereDate(
                    'cmp_received_date',
                    '>=',
                    $request->input('tarikh_mula')
                )
            )
            ->when(
                $request->filled('tarikh_tamat'),
                fn ($query) => $query->whereDate(
                    'cmp_received_date',
                    '<=',
                    $request->input('tarikh_tamat')
                )
            )
            ->when(
                $kolumSusunan === 'aset_bangunan',
                fn ($query) => $query->orderBy(
                    Bangunan::select('asb_name')
                        ->whereColumn('assets_building.asb_id', 'complaint.asb_id'),
                    $arahSusunan
                )
            )
            ->when(
                $kolumSusunan === 'nama_pengadu',
                fn ($query) => $query->orderByRaw(
                    "COALESCE(
                        (
                            SELECT tnt_name
                            FROM asb_tenant
                            WHERE asb_tenant.tnt_id = complaint.tnt_id
                        ),
                        complaint.cmp_reporter_name,
                        'Orang Awam'
                    ) {$arahSusunan}"
                )
            )
            ->when(
                ! in_array($kolumSusunan, ['aset_bangunan', 'nama_pengadu'], true),
                fn ($query) => $query->orderBy($kolumSusunan, $arahSusunan)
            );
    }

    private function dataEksport(string $jenis, Collection $rekod): array
    {
        return match ($jenis) {
            'bangunan' => [
                'kepala' => [
                    'Bil.',
                    'No. Rujukan',
                    'Nama Bangunan',
                    'Bandar',
                    'Status Premis',
                    'Status Penyelenggaraan',
                    'Penyewa Aktif',
                ],
                'baris' => $rekod->values()->map(fn ($item, $indeks) => [
                    $indeks + 1,
                    $item->asb_ref_no,
                    $item->asb_name,
                    $item->asb_city ?? '-',
                    Paparan::label($item->asb_premise_status),
                    Paparan::label($item->asb_maintenance_status),
                    $item->penyewa_aktif_count,
                ])->all(),
            ],

            'tanah' => [
                'kepala' => [
                    'Bil.',
                    'No. Rujukan',
                    'No. Lot',
                    'Lokasi',
                    'Status Hakmilik',
                    'Status Tanah',
                    'Keluasan (m²)',
                ],
                'baris' => $rekod->values()->map(fn ($item, $indeks) => [
                    $indeks + 1,
                    $item->asl_ref_no,
                    $item->asl_lot_no,
                    $item->asl_location,
                    Paparan::label($item->asl_status_hakmilik),
                    Paparan::label($item->asl_status_tanah),
                    number_format($item->asl_land_size, 2),
                ])->all(),
            ],
            'aduan' => [
                'kepala' => [
                    'Bil.',
                    'Tarikh Aduan',
                    'Aset Bangunan',
                    'No. Rujukan',
                    'Jenis Kerosakan',
                    'Pengadu',
                    'Status',
                    'Tarikh Penyelenggaraan',
                    'Tarikh Aduan Selesai',
                    'Bilangan Hari Aduan',
                ],
                'baris' => $rekod->values()->map(fn ($item, $indeks) => [
                    $indeks + 1,
                    $item->cmp_received_date?->format('d/m/Y') ?? '-',
                    $item->bangunan?->asb_name ?? '-',
                    $item->bangunan?->asb_ref_no ?? '-',
                    Paparan::label($item->cmp_damage_type),
                    $item->penyewa?->tnt_name
                        ?? $item->cmp_reporter_name
                        ?? 'Orang Awam',
                    Paparan::label($item->cmp_status),
                    $item->cmp_maintenance_date?->format('d/m/Y') ?? '-',
                    $item->cmp_completed_at?->format('d/m/Y') ?? '-',
                    $this->kiraBilanganHariAduan(
                        $item->cmp_received_date,
                        $item->cmp_completed_at
                    ),
                ])->all(),
            ],
            default => [
                'kepala' => [
                    'Bil.',
                    'Tarikh Aduan',
                    'Aset Bangunan',
                    'No. Rujukan',
                    'Jenis Kerosakan',
                    'Pengadu',
                    'Status',
                    'Tarikh Penyelenggaraan',
                ],
                'baris' => $rekod->values()->map(fn ($item, $indeks) => [
                    $indeks + 1,
                    $item->cmp_received_date?->format('d/m/Y') ?? '-',
                    $item->bangunan?->asb_name ?? '-',
                    $item->bangunan?->asb_ref_no ?? '-',
                    Paparan::label($item->cmp_damage_type),
                    $item->penyewa?->tnt_name
                        ?? $item->cmp_reporter_name
                        ?? 'Orang Awam',
                    Paparan::label($item->cmp_status),
                    $item->cmp_maintenance_date?->format('d/m/Y') ?? '-',
                ])->all(),
            ],
        };
    }

    private function kiraBilanganHariAduan($tarikhAduan, $tarikhSelesai): string
    {
        if (! $tarikhAduan || ! $tarikhSelesai) {
            return '-';
        }

        $hariBekerja = 0;

        $tarikhSemasa = $tarikhAduan->copy()->startOfDay();
        $tarikhAkhir = $tarikhSelesai->copy()->startOfDay();

        while ($tarikhSemasa->lt($tarikhAkhir)) {
            $tarikhSemasa->addDay();

            if (
                ! $tarikhSemasa->isFriday()
                && ! $tarikhSemasa->isSaturday()
            ) {
                $hariBekerja++;
            }
        }

        return $hariBekerja . ' hari';
    }

    private function sahkanAkses(Request $request, string $jenis): array
    {
        $konfigurasi = $this->konfigurasi($jenis);

        abort_unless(
            in_array($request->user()->usr_level, $konfigurasi['peranan'], true),
            403
        );

        return $konfigurasi;
    }

    private function konfigurasi(string $jenis): array
    {
        return match ($jenis) {
            'bangunan' => [
                'tajuk' => 'Laporan Aset Bangunan',
                'penerangan' => 'Senarai aset bangunan mengikut status premis dan penyelenggaraan.',
                'ikon' => 'bi-buildings-fill',
                'peranan' => ['SYSTEM', 'BPH'],
            ],
            'tanah' => [
                'tajuk' => 'Laporan Aset Tanah',
                'penerangan' => 'Senarai aset tanah mengikut status hakmilik dan status tanah.',
                'ikon' => 'bi-map-fill',
                'peranan' => ['SYSTEM', 'BPH', 'BP'],
            ],
            'aduan' => [
                'tajuk' => 'Laporan Aduan',
                'penerangan' => 'Senarai semua aduan aset bangunan yang direkodkan.',
                'ikon' => 'bi-clipboard2-pulse-fill',
                'peranan' => ['SYSTEM', 'BPH', 'KJ'],
            ],
            'penyelenggaraan' => [
                'tajuk' => 'Laporan Penyelenggaraan',
                'penerangan' => 'Senarai aduan yang telah diterima untuk tindakan penyelenggaraan.',
                'ikon' => 'bi-tools',
                'peranan' => ['SYSTEM', 'BPH', 'KJ'],
            ],
            default => abort(404),
        };
    }

    private function senaraiLaporan(string $peranan): array
    {
        $laporan = [];

        if (in_array($peranan, ['SYSTEM', 'BPH'], true)) {
            $laporan[] = [
                'kod' => 'bangunan',
                'tajuk' => 'Laporan Aset Bangunan',
                'penerangan' => 'Maklumat aset bangunan mengikut status premis, kategori dan penyelenggaraan.',
                'ikon' => 'bi-buildings-fill',
                'tema' => 'hijau',
                'jumlah' => Bangunan::count(),
                'label_jumlah' => 'aset bangunan',
            ];
        }

        if (in_array($peranan, ['SYSTEM', 'BPH', 'BP'], true)) {
            $laporan[] = [
                'kod' => 'tanah',
                'tajuk' => 'Laporan Aset Tanah',
                'penerangan' => 'Maklumat aset tanah mengikut status hakmilik dan status tanah.',
                'ikon' => 'bi-map-fill',
                'tema' => 'biru',
                'jumlah' => Tanah::count(),
                'label_jumlah' => 'aset tanah',
            ];
        }

        if (in_array($peranan, ['SYSTEM', 'BPH', 'KJ'], true)) {
            $laporan[] = [
                'kod' => 'aduan',
                'tajuk' => 'Laporan Aduan',
                'penerangan' => 'Rekod aduan aset bangunan mengikut status, tarikh dan jenis pengadu.',
                'ikon' => 'bi-clipboard2-pulse-fill',
                'tema' => 'jingga',
                'jumlah' => Aduan::count(),
                'label_jumlah' => 'aduan direkodkan',
            ];

            $laporan[] = [
                'kod' => 'penyelenggaraan',
                'tajuk' => 'Laporan Penyelenggaraan',
                'penerangan' => 'Rumusan aduan yang diterima dan sedang dalam tindakan penyelenggaraan.',
                'ikon' => 'bi-tools',
                'tema' => 'ungu',
                'jumlah' => Aduan::whereIn('cmp_status', [
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])->count(),
                'label_jumlah' => 'aduan dalam penyelenggaraan',
            ];
        }

        return $laporan;
    }

    private function namaFail(string $jenis, string $sambungan): string
    {
        return 'laporan-' . $jenis . '-' . now()->format('Ymd-His') . '.' . $sambungan;
    }
}