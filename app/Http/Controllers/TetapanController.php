<?php

namespace App\Http\Controllers;

use App\Models\Bangunan;
use App\Models\Tanah;
use App\Models\TetapanRujukan;
use App\Models\TetapanAduan;
use App\Models\TetapanPenyewa;
use App\Models\TetapanPenyelenggaraan;
use Illuminate\Validation\Rule;
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

    public function penyewa(): View
    {
        $tetapan = TetapanPenyewa::query()->first();

        return view('tetapan.penyewa', compact('tetapan'));
    }

    public function kemaskiniPenyewa(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'tpy_tempoh_hampir_tamat_perjanjian' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:365',
                ],
            ],
            [
                'tpy_tempoh_hampir_tamat_perjanjian.required' =>
                    'Sila masukkan tempoh hampir tamat perjanjian.',
                'tpy_tempoh_hampir_tamat_perjanjian.integer' =>
                    'Tempoh mesti dalam bentuk nombor.',
                'tpy_tempoh_hampir_tamat_perjanjian.min' =>
                    'Tempoh minimum ialah satu hari.',
                'tpy_tempoh_hampir_tamat_perjanjian.max' =>
                    'Tempoh maksimum ialah 365 hari.',
            ]
        );

        $tetapan = TetapanPenyewa::query()->firstOrNew();

        $tetapan->fill([
            'tpy_tempoh_hampir_tamat_perjanjian' =>
                $data['tpy_tempoh_hampir_tamat_perjanjian'],
            'updated_by' => $request->user()->usr_id,
        ]);

        if (! $tetapan->exists) {
            $tetapan->created_by = $request->user()->usr_id;
        }

        $tetapan->save();

        return redirect()
            ->route('tetapan.penyewa')
            ->with('berjaya', 'Tetapan penyewa berjaya dikemaskini.');
    }

    
    public function penyelenggaraan(): View
    {
        $tetapan = TetapanPenyelenggaraan::semasa();
        $peranan = TetapanPenyelenggaraan::PERANAN;

        return view('tetapan.penyelenggaraan', compact('tetapan', 'peranan'));
    }

    public function kemaskiniPenyelenggaraan(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'peranan_lihat' => ['nullable', 'array'],
                'peranan_lihat.*' => [Rule::in(TetapanPenyelenggaraan::PERANAN)],
                'peranan_isi' => ['nullable', 'array'],
                'peranan_isi.*' => [Rule::in(TetapanPenyelenggaraan::PERANAN)],
            ],
            [
                'in' => 'Peranan yang dipilih tidak sah.',
            ]
        );

        $perananIsi = array_values(array_unique($data['peranan_isi'] ?? []));

        // Peranan yang boleh isi, automatik boleh lihat
        $perananLihat = array_values(array_unique(array_merge(
            $data['peranan_lihat'] ?? [],
            $perananIsi
        )));

        $tetapan = TetapanPenyelenggaraan::semasa();

        $tetapan->update([
            'tpn_peranan_lihat' => $perananLihat,
            'tpn_peranan_isi' => $perananIsi,
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.penyelenggaraan')
            ->with('berjaya', 'Tetapan akses penyelenggaraan berjaya dikemaskini.');
    }
}
