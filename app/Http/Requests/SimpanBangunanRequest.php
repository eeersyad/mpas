<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanBangunanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->mempunyaiPeranan(['SYSTEM', 'BPH']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'asb_aset_warisan' => $this->boolean('asb_aset_warisan'),
        ]);
    }

    public function rules(): array
    {
        return [
            'asb_name' => ['required', 'string', 'max:255'],
            'asb_category' => ['required', 'in:PREMIS_PERNIAGAAN,TANDAS,DEWAN,PONDOK_BAS'],
            'asb_sub_category' => ['nullable', 'string', 'max:255'],
            'asb_add1' => ['required', 'string', 'max:255'],
            'asb_add2' => ['nullable', 'string', 'max:255'],
            'asb_postcode' => ['nullable', 'string', 'max:10'],
            'asb_distinct' => ['nullable', 'string', 'max:100'],
            'asb_mukim' => ['nullable', 'string', 'max:100'],
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
            'asb_collector_name' => ['nullable', 'string', 'max:255'],
            'asb_collector_position' => ['nullable', 'string', 'max:255'],
            'asb_collected_at' => ['nullable', 'date'],
            'asb_verifier_name' => ['nullable', 'string', 'max:255'],
            'asb_verifier_position' => ['nullable', 'string', 'max:255'],
            'asb_verified_at' => ['nullable', 'date'],

            'penyewa' => ['nullable', 'array'],
            'penyewa.*.tnt_id' => ['nullable', 'integer', 'exists:asb_tenant,tnt_id'],
            'penyewa.*.tnt_name' => ['required_with:penyewa.*.tnt_status', 'nullable', 'string', 'max:255'],
            'penyewa.*.tnt_acc_no' => ['nullable', 'string', 'max:100'],
            'penyewa.*.tnt_ic_no' => ['nullable', 'string', 'max:20'],
            'penyewa.*.tnt_start_date' => ['nullable', 'date'],
            'penyewa.*.tnt_end_date' => ['nullable', 'date', 'after_or_equal:penyewa.*.tnt_start_date'],
            'penyewa.*.tnt_status' => ['nullable', 'in:AKTIF,TIDAK_AKTIF'],
        ];
    }

    public function messages(): array
    {
        return [
            'asb_name.required' => 'Sila masukkan nama bangunan.',
            'asb_category.required' => 'Sila pilih kategori bangunan.',
            'asb_add1.required' => 'Sila masukkan alamat bangunan.',
            'asb_gmaps_location.url' => 'Sila masukkan pautan Google Maps yang sah.',
            'asb_maintenance_status.required' => 'Sila pilih status penyelenggaraan.',
            'asb_premise_status.required' => 'Sila pilih status premis.',
            'penyewa.*.tnt_name.required_with' => 'Sila masukkan nama penyewa.',
            'penyewa.*.tnt_end_date.after_or_equal' =>
                'Tarikh tamat sewaan tidak boleh lebih awal daripada tarikh mula sewaan.',
        ];
    }
}