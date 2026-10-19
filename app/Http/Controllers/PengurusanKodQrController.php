<?php

namespace App\Http\Controllers;

use App\Models\Bangunan;
use App\Services\KodQr;
use App\Support\Jadual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengurusanKodQrController extends Controller
{
    public function index(Request $request): View
    {
        $had = Jadual::had($request);

        $cari = trim((string) $request->input('cari', ''));
        $statusQr = $request->input('status_qr');

        $bangunan = Bangunan::query()
            ->with('kod')
            ->when($cari !== '', function ($query) use ($cari): void {
                $query->where(function ($query) use ($cari): void {
                    $query->where('asb_ref_no', 'like', "%{$cari}%")
                        ->orWhere('asb_name', 'like', "%{$cari}%");
                });
            })
            ->when(
                $statusQr === 'ACTIVE',
                fn ($query) => $query->whereHas(
                    'kod',
                    fn ($query) => $query->where('qr_status', 'ACTIVE')
                )
            )
            ->when(
                $statusQr === 'INACTIVE',
                fn ($query) => $query->whereHas(
                    'kod',
                    fn ($query) => $query->where('qr_status', 'INACTIVE')
                )
            )
            ->when(
                $statusQr === 'TIADA',
                fn ($query) => $query->whereDoesntHave('kod')
            )
            ->latest('asb_id')
            ->paginate($had)
            ->withQueryString();

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