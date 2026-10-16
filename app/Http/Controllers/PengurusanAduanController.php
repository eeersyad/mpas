<?php

namespace App\Http\Controllers;

use App\Http\Requests\TolakAduanRequest;
use App\Http\Requests\KemaskiniAduanRequest;
use App\Models\Aduan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengurusanAduanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $aduan = Aduan::query()
            ->with([
                'bangunan',
                'penyewa',
                'diterimaOleh',
                'disenggaraOleh',
            ])
            ->when(
                $user->usr_level === 'KJ',
                fn ($query) => $query->whereNotNull('cmp_accepted_by')
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'cmp_status',
                    $request->string('status')->toString()
                )
            )
            ->latest('cmp_received_date')
            ->paginate(10)
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
            'diterimaOleh',
            'disenggaraOleh',
        ]);

        return view('aduan.butiran', compact('aduan'));
    }

    public function terima(Request $request, Aduan $aduan): RedirectResponse
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

        return back()->with(
            'berjaya',
            'Aduan telah diterima dan dihantar kepada Jabatan Kejuruteraan.'
        );
    }

    public function tolak(
        TolakAduanRequest $request,
        Aduan $aduan
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

        return back()->with(
            'berjaya',
            'Aduan telah ditolak dan ditandakan sebagai selesai.'
        );
    }

    public function kemaskiniPenyelenggaraan(
        KemaskiniAduanRequest $request,
        Aduan $aduan
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

        $aduan->update([
            'cmp_status' => $status,
            'cmp_maintenance_date' => $request->input('cmp_maintenance_date'),
            'cmp_completion_note' => $request->input('cmp_completion_note'),
            'cmp_completed_at' => $status === 'SELESAI' ? now() : null,
            'cmp_maintained_by' => $request->user()->usr_id,
            'updated_by' => $request->user()->usr_id,
        ]);

        return back()->with(
            'berjaya',
            $request->cmp_status === 'SELESAI'
                ? 'Aduan berjaya ditandakan sebagai selesai.'
                : 'Status penyelenggaraan aduan berjaya dikemaskini.'
        );
    }
}