<?php

namespace App\Http\Controllers;

use App\Models\PegawaiAset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TetapanPegawaiController extends Controller
{
    public function index(): View
    {
        $pegawai = PegawaiAset::query()
            ->orderByRaw("pgw_status = 'ACTIVE' DESC")
            ->orderBy('pgw_nama')
            ->get();

        return view('tetapan.pegawai', compact('pegawai'));
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $this->sahkan($request);

        PegawaiAset::create([
            ...$data,
            'created_by' => $request->user()->usr_id,
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.pegawai')
            ->with('berjaya', "Pegawai {$data['pgw_nama']} berjaya ditambah.");
    }

    public function kemaskini(Request $request, PegawaiAset $pegawai): RedirectResponse
    {
        $data = $this->sahkan($request, $pegawai);

        $pegawai->update([
            ...$data,
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.pegawai')
            ->with('berjaya', "Maklumat pegawai {$pegawai->pgw_nama} berjaya dikemaskini.");
    }

    private function sahkan(Request $request, ?PegawaiAset $pegawai = null): array
    {
        $request->merge([
            'pgw_nama' => $this->formatTajuk($request->input('pgw_nama')),
            'pgw_jawatan' => $this->formatTajuk($request->input('pgw_jawatan')),
            'pgw_status' => $request->boolean('pgw_status') ? 'ACTIVE' : 'INACTIVE',
        ]);

        return $request->validate(
            [
                'pgw_nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tetapan_pegawai', 'pgw_nama')
                        ->where('pgw_jawatan', $request->input('pgw_jawatan'))
                        ->ignore($pegawai?->pgw_id, 'pgw_id'),
                ],
                'pgw_jawatan' => ['required', 'string', 'max:255'],
                'pgw_peranan' => ['required', Rule::in(PegawaiAset::SENARAI_PERANAN)],
                'pgw_status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
            ],
            [
                'pgw_nama.required' => 'Sila masukkan nama pegawai.',
                'pgw_nama.unique' => 'Pegawai dengan nama dan jawatan ini telah wujud.',
                'pgw_jawatan.required' => 'Sila masukkan jawatan pegawai.',
                'pgw_peranan.required' => 'Sila pilih peranan pegawai.',
                'pgw_peranan.in' => 'Peranan yang dipilih tidak sah.',
            ]
        );
    }

    private function formatTajuk(mixed $nilai): ?string
    {
        if (! is_string($nilai) || trim($nilai) === '') {
            return null;
        }

        return mb_convert_case(mb_strtolower(trim($nilai), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
