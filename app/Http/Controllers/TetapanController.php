<?php

namespace App\Http\Controllers;

use App\Models\Bangunan;
use App\Models\Tanah;
use App\Models\TetapanRujukan;
use App\Models\TetapanAduan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TetapanController extends Controller
{
    public function rujukan(): View
    {
        $tetapan = TetapanRujukan::query()
            ->whereIn('ruj_jenis', ['TANAH', 'BANGUNAN'])
            ->get()
            ->keyBy('ruj_jenis');

        $rujukanTerakhir = [
            'TANAH' => Tanah::query()
                ->latest('asl_id')
                ->value('asl_ref_no'),

            'BANGUNAN' => Bangunan::query()
                ->latest('asb_id')
                ->value('asb_ref_no'),
        ];

        return view('tetapan.rujukan', compact('tetapan', 'rujukanTerakhir'));
    }

    public function kemaskiniRujukan(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'tanah_awalan' => ['required', 'string', 'max:50'],
                'tanah_nombor_mula' => ['required', 'integer', 'min:1'],
                'tanah_panjang_nombor' => ['required', 'integer', 'min:1', 'max:10'],
                'bangunan_awalan' => ['required', 'string', 'max:50'],
                'bangunan_nombor_mula' => ['required', 'integer', 'min:1'],
                'bangunan_panjang_nombor' => ['required', 'integer', 'min:1', 'max:10'],
            ],
            [
                'required' => 'Sila lengkapkan maklumat tetapan.',
                'integer' => 'Nilai mesti dalam bentuk nombor.',
                'min' => 'Nilai yang dimasukkan tidak sah.',
                'max' => 'Nilai yang dimasukkan melebihi had dibenarkan.',
            ]
        );

        $this->simpanTetapan(
            'TANAH',
            $data['tanah_awalan'],
            $data['tanah_nombor_mula'],
            $data['tanah_panjang_nombor'],
            $request->user()->usr_id
        );

        $this->simpanTetapan(
            'BANGUNAN',
            $data['bangunan_awalan'],
            $data['bangunan_nombor_mula'],
            $data['bangunan_panjang_nombor'],
            $request->user()->usr_id
        );

        return redirect()
            ->route('tetapan.rujukan')
            ->with('berjaya', 'Tetapan nombor rujukan berjaya dikemaskini.');
    }

    private function simpanTetapan(
        string $jenis,
        string $awalan,
        int $nomborMula,
        int $panjangNombor,
        int $userId
    ): void {
        $tetapan = TetapanRujukan::firstOrNew([
            'ruj_jenis' => $jenis,
        ]);

        $maklumat = [
            'ruj_awalan' => strtoupper(trim($awalan)),
            'ruj_panjang_nombor' => $panjangNombor,
            'updated_by' => $userId,
        ];

        if (! $tetapan->exists) {
            $maklumat['ruj_nombor_mula'] = $nomborMula;
            $maklumat['created_by'] = $userId;
        }

        if ($tetapan->exists && $tetapan->ruj_nombor_semasa === null) {
            $maklumat['ruj_nombor_mula'] = $nomborMula;
        }

        $tetapan->fill($maklumat)->save();
    }

    public function aduan(): View
    {
        $tetapan = TetapanAduan::query()->first();

        return view('tetapan.aduan', compact('tetapan'));
    }

    public function kemaskiniAduan(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'adu_tempoh_papar_selesai' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:365',
                ],
            ],
            [
                'adu_tempoh_papar_selesai.required' =>
                    'Sila masukkan tempoh paparan aduan selesai.',
                'adu_tempoh_papar_selesai.integer' =>
                    'Tempoh mesti dalam bentuk nombor.',
                'adu_tempoh_papar_selesai.min' =>
                    'Tempoh minimum ialah satu hari.',
                'adu_tempoh_papar_selesai.max' =>
                    'Tempoh maksimum ialah 365 hari.',
            ]
        );

        $tetapan = TetapanAduan::query()->firstOrNew();

        $tetapan->fill([
            'adu_tempoh_papar_selesai' =>
                $data['adu_tempoh_papar_selesai'],
            'updated_by' => $request->user()->usr_id,
        ]);

        if (! $tetapan->exists) {
            $tetapan->created_by = $request->user()->usr_id;
        }

        $tetapan->save();

        return redirect()
            ->route('tetapan.aduan')
            ->with('berjaya', 'Tetapan aduan awam berjaya dikemaskini.');
    }
}