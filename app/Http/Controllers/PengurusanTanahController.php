<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanTanahRequest;
use App\Models\Aset;
use App\Models\Tanah;
use App\Services\Rujukan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class PengurusanTanahController extends Controller
{
    public function index(): View
    {
        $tanah = Tanah::query()
            ->with('aset')
            ->latest('asl_id')
            ->paginate(10);

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