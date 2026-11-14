<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Penyelenggaraan;
use App\Models\TetapanPenyelenggaraan;
use App\Services\Pemeriksaan;

class SimpanBangunanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->mempunyaiPeranan(['SYSTEM', 'BPH']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $medanFormatTajuk = [
            'asb_name',
            'asb_add1',
            'asb_add2',
            'asb_city',
        ];

        $maklumatTajuk = [];

        foreach ($medanFormatTajuk as $medan) {
            $nilai = $this->input($medan);

            if (is_string($nilai)) {
                $maklumatTajuk[$medan] = mb_convert_case(
                    mb_strtolower(trim($nilai), 'UTF-8'),
                    MB_CASE_TITLE,
                    'UTF-8'
                );
            }
        }

        $this->merge([
            ...$maklumatTajuk,
            'asb_aset_warisan' => $this->boolean('asb_aset_warisan'),
        ]);
        
        if (! TetapanPenyelenggaraan::bolehIsi($this->user())) {
            $this->merge([
                'pemeriksaan' => ['mod' => 'TIADA'],
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'asb_name' => ['required', 'string', 'max:255'],
            'asb_no_file' => ['nullable', 'string', 'max:100'],
            'asb_category' => ['required', 'in:PREMIS_PERNIAGAAN,TANDAS,DEWAN,PONDOK_BAS'],
            'asb_sub_category' => ['nullable', 'string', 'max:255'],
            'asb_add1' => ['required', 'string', 'max:255'],
            'asb_add2' => ['nullable', 'string', 'max:255'],
            'asb_postcode' => ['nullable', 'string', 'max:10'],
            'asb_distinct' => ['nullable', 'string', 'max:100'],
            'asb_mukim' => [
                'nullable',
                'string',
                Rule::in([
                    'Alor Limbat',
                    'Bukit Payung',
                    'Jerong',
                    'Merchang',
                    'Pulau Kerengga',
                    'Rusila',
                ]),
            ],
            'asb_city' => ['nullable', 'string', 'max:100'],
            'asb_state' => ['nullable', 'string', 'max:100'],
            'asb_country' => ['nullable', 'string', 'max:100'],

            'asb_gmaps_location' => ['nullable', 'url', 'max:2000'],
            'asb_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'asb_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'asb_agency' => ['nullable', 'string', 'max:255'],
            'asb_ministry' => ['nullable', 'string', 'max:255'],
            'asb_dept' => ['nullable', 'string', 'max:255'],
            'asb_ptj_code' => ['nullable', 'string', 'max:100'],

            'asb_size' => ['nullable', 'numeric', 'min:0'],
            'asb_aset_warisan' => ['boolean'],
            'asb_total_block' => ['nullable', 'integer', 'min:0'],
            'asb_total_ext_structure' => ['nullable', 'integer', 'min:0'],
            'asb_tnb_acc_no' => ['nullable', 'string', 'max:100'],
            'asb_satu_acc_no' => ['nullable', 'string', 'max:100'],

            'asb_kos_siap_bina_asal' => ['nullable', 'numeric', 'min:0'],
            'asb_kos_tambahan' => ['nullable', 'numeric', 'min:0'],
            'asb_completion_date' => ['nullable', 'date'],
            'asb_financing_source' => ['nullable', 'string', 'max:255'],
            'asb_current_value' => ['nullable', 'numeric', 'min:0'],
            'asb_assessment_date' => ['nullable', 'date'],

            'asb_maintenance_status' => ['required', 'in:RENDAH,SEDERHANA,KRITIKAL'],
            'asb_premise_status' => [
                'required',
                'in:SEWA,TIDAK_DISEWA,PAJAK,TIDAK_DIPAJAK,KEGUNAAN_MDM,DILUPUS',
            ],
            'asb_collector_id' => ['nullable', 'integer', $this->rulePegawai('collector', ['PENGUMPUL', 'KEDUA_DUA'])],
            'asb_collected_at' => ['nullable', 'date'],
            'asb_verifier_id' => ['nullable', 'integer', $this->rulePegawai('verifier', ['PENGESAH', 'KEDUA_DUA'])],
            'asb_verified_at' => ['nullable', 'date'],

            'penyewa' => ['nullable', 'array'],
            'penyewa.*.tnt_id' => ['nullable', 'integer', 'exists:asb_tenant,tnt_id'],
            'penyewa.*.tnt_name' => [
                'nullable',
                'required_with:penyewa.*.tnt_status',
                'string',
                'max:255',
            ],
            'penyewa.*.tnt_acc_no' => [
                'nullable',
                'required_with:penyewa.*.tnt_status',
                'string',
                'max:100',
            ],
            'penyewa.*.tnt_ic_no' => [
                'nullable',
                'required_with:penyewa.*.tnt_status',
                'string',
                'max:20',
            ],
            'penyewa.*.tnt_mula_perjanjian' => ['nullable', 'date'],
            'penyewa.*.tnt_tamat_perjanjian' => [
                'nullable',
                'date',
                'after_or_equal:penyewa.*.tnt_mula_perjanjian',
            ],
            'penyewa.*.tnt_status' => ['nullable', 'in:AKTIF,TIDAK_AKTIF'],

            'dokumen' => ['nullable', 'array', 'max:10'],
            'dokumen.*.jenis' => [
                'nullable',
                'required_with:dokumen.*.fail',
                'in:GERAN,PELAN,SURAT_KELULUSAN,PERJANJIAN_SEWA,LAPORAN_PENILAIAN,GAMBAR,LAIN_LAIN',
            ],
            'dokumen.*.fail' => [
                'nullable',
                'required_with:dokumen.*.jenis',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],
        ];

        return array_merge($rules, $this->rulesPemeriksaan());
    }

    /**
     * Pegawai mesti wujud, mempunyai peranan yang betul, dan aktif.
     * Pengecualian: pegawai yang sudah dipilih untuk bangunan ini dibenarkan
     * walaupun kini tidak aktif, supaya kemaskini lain tidak tersekat.
     */
    private function rulePegawai(string $jenis, array $peranan): \Illuminate\Validation\Rules\Exists
    {
        $semasa = $this->route('bangunan')?->{"asb_{$jenis}_id"};

        return Rule::exists('tetapan_pegawai', 'pgw_id')
            ->whereIn('pgw_peranan', $peranan)
            ->where(function ($query) use ($semasa) {
                $query->where(function ($q) use ($semasa) {
                    $q->where('pgw_status', 'ACTIVE')
                        ->orWhere('pgw_id', $semasa ?? 0);
                });
            });
    }

    private function rulesPemeriksaan(): array
    {
        $komponen = app(Pemeriksaan::class)->komponenAktif();

        $modDibenarkan = match (true) {
            ! TetapanPenyelenggaraan::bolehIsi($this->user()),
            $komponen->isEmpty() => ['TIADA'],
            $this->isMethod('post') => ['TIADA', 'BARU'],
            default => ['TIADA', 'BARU', 'KEMASKINI'],
        };

        $rules = [
            'pemeriksaan' => ['required', 'array'],
            'pemeriksaan.mod' => ['required', Rule::in($modDibenarkan)],
            'pemeriksaan.asm_inspection_date' => [
                'exclude_if:pemeriksaan.mod,TIADA',
                'required',
                'date',
                'before_or_equal:today',
            ],
            'pemeriksaan.asm_note' => [
                'exclude_if:pemeriksaan.mod,TIADA',
                'nullable',
                'string',
                'max:2000',
            ],
            'pemeriksaan.nilai' => [
                'exclude_if:pemeriksaan.mod,TIADA',
                'required',
                'array',
            ],
        ];

        foreach ($komponen as $item) {
            foreach ($item->itemAktif() as $baris) {
                $rules["pemeriksaan.nilai.{$item->amc_id}.{$baris['id']}"] = [
                    'exclude_if:pemeriksaan.mod,TIADA',
                    'required',
                    Rule::in(Penyelenggaraan::NILAI),
                ];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'pemeriksaan.mod.in' => 'Pilihan rekod pemeriksaan tidak sah.',
            'asb_collector_id.exists' => 'Pegawai pengumpul yang dipilih tidak sah atau tidak aktif.',
            'asb_verifier_id.exists' => 'Pegawai pengesah yang dipilih tidak sah atau tidak aktif.',
            'pemeriksaan.asm_inspection_date.required' => 'Sila masukkan tarikh pemeriksaan.',
            'pemeriksaan.asm_inspection_date.date' => 'Tarikh pemeriksaan tidak sah.',
            'pemeriksaan.asm_inspection_date.before_or_equal' => 'Tarikh pemeriksaan tidak boleh melebihi hari ini.',
            'pemeriksaan.asm_note.max' => 'Catatan pemeriksaan tidak boleh melebihi 2000 aksara.',
            'pemeriksaan.nilai.required' => 'Sila lengkapkan keputusan pemeriksaan.',
            'pemeriksaan.nilai.*.*.required' => 'Sila pilih keputusan bagi setiap item pemeriksaan.',
            'pemeriksaan.nilai.*.*.in' => 'Keputusan pemeriksaan tidak sah.',
            'asb_name.required' => 'Sila masukkan nama bangunan.',
            'asb_category.required' => 'Sila pilih kategori bangunan.',
            'asb_add1.required' => 'Sila masukkan alamat bangunan.',
            'asb_gmaps_location.url' => 'Sila masukkan pautan Google Maps yang sah.',
            'asb_maintenance_status.required' => 'Sila pilih status penyelenggaraan.',
            'asb_premise_status.required' => 'Sila pilih status premis.',
            'penyewa.*.tnt_name.required_with' => 'Sila masukkan nama penyewa.',
            'penyewa.*.tnt_tamat_perjanjian.after_or_equal' =>
                'Tarikh tamat perjanjian tidak boleh lebih awal daripada tarikh mula perjanjian.',
            'penyewa.*.tnt_ic_no.required_with' =>
                'Sila masukkan nombor kad pengenalan atau nombor syarikat penyewa.',
            'penyewa.*.tnt_acc_no.required_with' =>
                'Sila masukkan nombor akaun penyewa.',
            'dokumen.max' => 'Maksimum 10 dokumen boleh dimuat naik dalam satu masa.',
            'dokumen.*.jenis.required_with' => 'Sila pilih jenis dokumen.',
            'dokumen.*.jenis.in' => 'Jenis dokumen yang dipilih tidak sah.',
            'dokumen.*.fail.required_with' => 'Sila pilih fail untuk dokumen ini.',
            'dokumen.*.fail.mimes' => 'Dokumen mestilah dalam format PDF, JPG, JPEG, PNG atau WEBP.',
            'dokumen.*.fail.max' => 'Saiz setiap dokumen tidak boleh melebihi 10MB.',
        ];
    }
}