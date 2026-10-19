<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanTanahRequest;
use App\Models\Aset;
use App\Models\Tanah;
use App\Services\Rujukan;
use App\Support\Jadual;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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

    public function store(SimpanTanahRequest $request, Rujukan $rujukan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $rujukan): void {
                $aset = Aset::create([
                    'ast_name' => 'TANAH',
                    'created_by' => $request->user()->usr_id,
                ]);

                Tanah::create([
                    ...$request->validated(),
                    'ast_id' => $aset->ast_id,
                    'asl_ref_no' => $rujukan->jana(
                        'TANAH',
                        $request->user()->usr_id
                    ),
                ]);
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
        $tanah->load('aset');

        return view('tanah.butiran', compact('tanah'));
    }

    public function edit(Tanah $tanah): View
    {
        return view('tanah.kemaskini', compact('tanah'));
    }

    public function update(SimpanTanahRequest $request, Tanah $tanah): RedirectResponse
    {
        DB::transaction(function () use ($request, $tanah): void {
            $tanah->update($request->validated());

            $tanah->aset()->update([
                'updated_by' => $request->user()->usr_id,
            ]);
        });

        return redirect()
            ->route('tanah.index')
            ->with('berjaya', 'Maklumat aset tanah berjaya dikemaskini.');
    }
}