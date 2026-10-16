<?php

namespace App\Http\Controllers;

use App\Models\Bangunan;
use App\Services\KodQr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengurusanKodQrController extends Controller
{
    public function index(): View
    {
        $bangunan = Bangunan::query()
            ->with('kod')
            ->latest('asb_id')
            ->paginate(10);

        return view('kodqr.senarai', compact('bangunan'));
    }

    public function show(
        Request $request,
        Bangunan $bangunan,
        KodQr $kodQr
    ): View {
        $kod = $kodQr->jana(
            $bangunan,
            $request->user()->usr_id
        );

        $pautanAduan = rtrim((string) config('app.url'), '/') . '/laporan/' . $kod->qr_code_value;

        return view('kodqr.butiran', compact(
            'bangunan',
            'kod',
            'pautanAduan'
        ));
    }

    public function kemaskiniStatus(
        Request $request,
        Bangunan $bangunan,
        KodQr $kodQr
    ): RedirectResponse {
        $data = $request->validate([
            'qr_status' => ['required', 'in:ACTIVE,INACTIVE'],
        ]);

        $kod = $kodQr->jana(
            $bangunan,
            $request->user()->usr_id
        );

        $kod->update([
            'qr_status' => $data['qr_status'],
        ]);

        return redirect()
            ->route('kodqr.show', $bangunan)
            ->with('berjaya', 'Status kod QR berjaya dikemaskini.');
    }
}