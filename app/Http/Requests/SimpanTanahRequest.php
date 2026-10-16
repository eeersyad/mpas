<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'asl_location' => ['required', 'string', 'max:255'],
            'asl_land_size' => ['required', 'numeric', 'min:0'],

            'asl_hakmilik_no' => ['nullable', 'string', 'max:100'],
            'asl_status_hakmilik' => ['nullable', 'in:HAKMILIK,RIZAB,STRATA,LAIN_LAIN'],
            'asl_hakmilik_type' => ['nullable', 'in:SEWA,TIDAK_DISEWA,PAJAK,TIDAK_DIPAJAK,KEKAL'],
            'asl_tarikh_terima_hakmilik' => ['nullable', 'date'],
            'asl_tarikh_serah_balik_hakmilik' => ['nullable', 'date', 'after_or_equal:asl_tarikh_terima_hakmilik'],

            'asl_gmaps_location' => ['nullable', 'url', 'max:2000'],
            'asl_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'asl_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'asl_land_price' => ['nullable', 'numeric', 'min:0'],
            'asl_land_skp' => ['nullable', 'numeric', 'min:0'],
            'asl_cukaiT' => ['nullable', 'numeric', 'min:0'],
            'asl_status_cukaiT' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'asl_lot_no.required' => 'Sila masukkan nombor lot.',
            'asl_location.required' => 'Sila masukkan lokasi tanah.',
            'asl_land_size.required' => 'Sila masukkan keluasan tanah.',
            'asl_land_size.numeric' => 'Keluasan tanah mesti dalam bentuk nombor.',
            'asl_gmaps_location.url' => 'Sila masukkan pautan Google Maps yang sah.',
            'asl_latitude.between' => 'Latitud mesti berada antara -90 hingga 90.',
            'asl_longitude.between' => 'Longitud mesti berada antara -180 hingga 180.',
            'asl_tarikh_serah_balik_hakmilik.after_or_equal' =>
                'Tarikh serah balik tidak boleh lebih awal daripada tarikh terima hakmilik.',
        ];
    }
}