<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tanah;
use Illuminate\Validation\Rule;

class SimpanTanahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->mempunyaiPeranan(['SYSTEM', 'BPH', 'BP']) ?? false;
    }

    public function rules(): array
    {
        return [
            'asl_lot_no' => ['required', 'string', 'max:100'],
            'asl_no_file' => ['nullable', 'string', 'max:100'],
            'asl_location' => ['required', 'string', 'max:255'],
            'asl_mukim' => ['required', Rule::in(Tanah::SENARAI_MUKIM)],
            'asl_note' => ['nullable', 'string', 'max:2000'],
            'asl_land_size' => ['required', 'numeric', 'min:0'],

            'asl_hakmilik_no' => ['nullable', 'string', 'max:100'],
            'asl_status_hakmilik' => ['nullable', 'in:HAKMILIK,RIZAB,STRATA,LAIN_LAIN'],
            'asl_status_tanah' => ['required', 'in:SEWA,TIDAK_DISEWA,PAJAK,TIDAK_DIPAJAK'],
            'asl_tarikh_terima_hakmilik' => ['nullable', 'date'],
            'asl_tarikh_serah_balik_hakmilik' => ['nullable', 'date', 'after_or_equal:asl_tarikh_terima_hakmilik'],

            'asl_gmaps_location' => ['nullable', 'url', 'max:2000'],
            'asl_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'asl_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'asl_land_price' => ['nullable', 'numeric', 'min:0'],
            'asl_land_skp' => ['nullable', 'numeric', 'min:0'],
            'asl_cukaiT' => ['nullable', 'numeric', 'min:0'],
            'asl_status_cukaiT' => ['nullable', Rule::in(Tanah::SENARAI_STATUS_CUKAI)],

            'dokumen' => ['nullable', 'array', 'max:4'],
            'dokumen.*.jenis' => [
                'nullable',
                'required_with:dokumen.*.fail',
                'in:GERAN,PELAN_LOKASI,SURAT_KELULUSAN,RESIT_CUKAI,GAMBAR,LAIN_LAIN',
            ],
            'dokumen.*.fail' => [
                'nullable',
                'required_with:dokumen.*.jenis',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'asl_lot_no.required' => 'Sila masukkan nombor lot.',
            'asl_location.required' => 'Sila masukkan lokasi tanah.',
            'asl_mukim.required' => 'Sila pilih mukim.',
            'asl_mukim.in' => 'Mukim yang dipilih tidak sah.',
            'asl_note.max' => 'Catatan tidak boleh melebihi 2000 aksara.',
            'asl_status_cukaiT.in' => 'Status cukai tanah yang dipilih tidak sah.',
            'asl_land_size.required' => 'Sila masukkan keluasan tanah.',
            'asl_land_size.numeric' => 'Keluasan tanah mesti dalam bentuk nombor.',
            'asl_gmaps_location.url' => 'Sila masukkan pautan Google Maps yang sah.',
            'asl_latitude.between' => 'Latitud mesti berada antara -90 hingga 90.',
            'asl_longitude.between' => 'Longitud mesti berada antara -180 hingga 180.',
            'asl_tarikh_serah_balik_hakmilik.after_or_equal' =>
                'Tarikh serah balik tidak boleh lebih awal daripada tarikh terima hakmilik.',
            'asl_status_tanah.required' => 'Status tanah diperlukan.',
            'asl_status_tanah.in' => 'Status tanah yang dipilih tidak sah.',
            
            'dokumen.max' => 'Maksimum 4 dokumen boleh dimuat naik bagi satu aset tanah.',
            'dokumen.*.jenis.required_with' => 'Sila pilih jenis dokumen.',
            'dokumen.*.jenis.in' => 'Jenis dokumen yang dipilih tidak sah.',
            'dokumen.*.fail.required_with' => 'Sila pilih fail dokumen.',
            'dokumen.*.fail.mimes' => 'Dokumen mestilah dalam format PDF, JPG, JPEG, PNG atau WEBP.',
            'dokumen.*.fail.max' => 'Saiz setiap dokumen tidak boleh melebihi 10MB.',
        ];
    }
}