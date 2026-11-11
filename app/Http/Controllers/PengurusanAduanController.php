<?php

namespace App\Http\Controllers;

use App\Http\Requests\TolakAduanRequest;
use App\Http\Requests\KemaskiniAduanRequest;
use App\Models\Aduan;
use App\Support\Jadual;
use App\Models\Bangunan;
use App\Support\Susunan;
use App\Services\NotifikasiAduan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengurusanAduanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $had = Jadual::had($request);

        $cari = trim((string) $request->input('cari', ''));
        $jenisPengadu = $request->input('jenis_pengadu');
        $statusAduanAktif = $user->usr_level === 'KJ'
            ? ['DITERIMA', 'DALAM_PENYELENGGARAAN']
            : ['BARU', 'DITERIMA', 'DALAM_PENYELENGGARAAN'];

        [$kolumSusunan, $arahSusunan] = Susunan::dapatkan(
            $request,
            [
                'tarikh' => 'cmp_received_date',
                'aset' => 'aset_bangunan',
                'kerosakan' => 'cmp_damage_type',
                'status' => 'cmp_status',
                'terkini' => 'cmp_received_date',
            ],
            'terkini'
        );

        $aduan = Aduan::query()
            ->with([
                'bangunan',
                'penyewa',
                'diterimaOleh',
                'disenggaraOleh',
            ])
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
            ->when($jenisPengadu === 'PENYEWA', fn ($query) => $query->whereNotNull('tnt_id'))
            ->when($jenisPengadu === 'AWAM', fn ($query) => $query->whereNull('tnt_id'))
            ->when(
                $user->usr_level === 'KJ',
                fn ($query) => $query->whereNotNull('cmp_accepted_by')
            )
            ->when(
                $request->boolean('aktif'),
                fn ($query) => $query->whereIn('cmp_status', $statusAduanAktif)
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'cmp_status',
                    $request->string('status')->toString()
                )
            )
            ->when(
                $kolumSusunan === 'aset_bangunan',
                fn ($query) => $query->orderBy(
                    Bangunan::select('asb_name')
                        ->whereColumn('assets_building.asb_id', 'complaint.asb_id'),
                    $arahSusunan
                ),
                fn ($query) => $query->orderBy($kolumSusunan, $arahSusunan)
            )
            ->paginate($had)
            ->withQueryString();

        return view('aduan.senarai', compact('aduan'));
    }

    public function show(Request $request, Aduan $aduan): View
    {
        $user = $request->user();

        if ($user->usr_level === 'KJ' && $aduan->cmp_accepted_by === null) {
            abort(403);
        }

        $aduan->load([
            'bangunan',
            'penyewa',
            'gambar',
            'gambarSiap.dimuatNaikOleh',
            'diterimaOleh',
            'disenggaraOleh',
        ]);

        return view('aduan.butiran', compact('aduan'));
    }

    public function terima(Request $request, Aduan $aduan, NotifikasiAduan $notifikasiAduan): RedirectResponse
    {
        if ($aduan->cmp_status !== 'BARU') {
            return back()->with(
                'ralat',
                'Aduan ini telah diproses sebelum ini.'
            );
        }

        $aduan->update([
            'cmp_status' => 'DITERIMA',
            'cmp_accepted_by' => $request->user()->usr_id,
            'cmp_accepted_at' => now(),
            'updated_by' => $request->user()->usr_id,
        ]);

        $notifikasiAduan->aduanDiterima($aduan);

        return back()->with(
            'berjaya',
            'Aduan telah diterima dan dihantar kepada Jabatan Kejuruteraan.'
        );
    }

    public function tolak(
        TolakAduanRequest $request,
        Aduan $aduan,
        NotifikasiAduan $notifikasiAduan
    ): RedirectResponse {
        if ($aduan->cmp_status !== 'BARU') {
            return back()->with(
                'ralat',
                'Aduan ini telah diproses sebelum ini.'
            );
        }

        $aduan->update([
            'cmp_status' => 'SELESAI',
            'cmp_completion_note' => 'Aduan ditolak oleh BPH: ' .
                $request->string('cmp_completion_note')->toString(),
            'updated_by' => $request->user()->usr_id,
        ]);

        $notifikasiAduan->maklumkanPengadu($aduan, 'DITOLAK');

        return back()->with(
            'berjaya',
            'Aduan telah ditolak dan ditandakan sebagai selesai.'
        );
    }

    public function kemaskiniPenyelenggaraan(
        KemaskiniAduanRequest $request,
        Aduan $aduan,
        NotifikasiAduan $notifikasiAduan
    ): RedirectResponse {
        if ($aduan->cmp_accepted_by === null) {
            return back()->with(
                'ralat',
                'Hanya aduan yang telah diterima boleh dikemaskini oleh KJ.'
            );
        }

        if ($aduan->cmp_status === 'SELESAI') {
            return back()->with(
                'ralat',
                'Aduan yang telah selesai tidak boleh dikemaskini lagi.'
            );
        }

        $status = $request->string('cmp_status')->toString();

        DB::transaction(function () use ($request, $aduan, $status): void {
            $aduan->update([
                'cmp_status' => $status,
                'cmp_maintenance_date' => $request->input('cmp_maintenance_date'),
                'cmp_completion_note' => $request->input('cmp_completion_note'),
                'cmp_completed_at' => $status === 'SELESAI' ? now() : null,
                'cmp_maintained_by' => $request->user()->usr_id,
                'updated_by' => $request->user()->usr_id,
            ]);

            if ($status !== 'SELESAI' || ! $request->hasFile('gambar_siap')) {
                return;
            }

            $gambarSiap = collect($request->file('gambar_siap'))
                ->filter()
                ->values();

            foreach ($gambarSiap as $indeks => $gambar) {
                $laluan = $gambar->store(
                    'aduan-siap/' . $aduan->cmp_id,
                    'public'
                );

                $aduan->gambarSiap()->create([
                    'cmpcimg_name' => $gambar->getClientOriginalName(),
                    'cmpcimg_path' => $laluan,
                    'cmpcimg_order' => $indeks + 1,
                    'created_by' => $request->user()->usr_id,
                ]);
            }
        });

        $notifikasiAduan->aduanDikemaskini(
            $aduan,
            $request->input('cmp_status')
        );

        if ($status === 'SELESAI') {
            $notifikasiAduan->maklumkanPengadu($aduan->refresh(), 'SELESAI');
        }

        return back()->with(
            'berjaya',
            $request->cmp_status === 'SELESAI'
                ? 'Aduan berjaya ditandakan sebagai selesai.'
                : 'Status penyelenggaraan aduan berjaya dikemaskini.'
        );
    }
}