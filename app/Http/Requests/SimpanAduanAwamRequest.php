<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanAduanAwamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_pengadu' => ['required', 'in:PENYEWA,ORANG_AWAM'],

            'tnt_id' => [
                'nullable',
                'required_if:jenis_pengadu,PENYEWA',
                'integer',
            ],
            'cmp_reporter_name' => [
                'nullable',
                'required_if:jenis_pengadu,ORANG_AWAM',
                'string',
                'max:255',
            ],
            'cmp_reporter_phone' => [
                'nullable',
                'required_if:jenis_pengadu,ORANG_AWAM',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]{8,20}$/',
            ],

            'cmp_reporter_email' => ['required', 'email:rfc', 'max:150'],

            'cmp_damage_type' => ['required', 'in:STRUKTUR BANGUNAN,ELEKTRIK,PAIP DAN SANITARI,BUMBUNG,PINTU ATAU TINGKAP'],
            'cmp_note' => ['required', 'string', 'max:2000'],

            'gambar' => ['nullable', 'array', 'max:4'],
            'gambar.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_pengadu.required' => __('Sila pilih kategori pengadu.'),
            'tnt_id.required_if' => __('Sila pilih nama penyewa.'),
            'cmp_damage_type.required' => __('Sila pilih jenis kerosakan.'),
            'cmp_damage_type.in' => __('Jenis kerosakan tidak sah.'),
            'cmp_note.required' => __('Sila masukkan keterangan aduan.'),
            'gambar.max' => __('Maksimum empat gambar boleh dimuat naik.'),
            'gambar.*.max' => __('Saiz setiap gambar tidak boleh melebihi 5 MB.'),
            'cmp_reporter_name.required_if' => __('Sila masukkan nama pengadu.'),
            'cmp_reporter_phone.required_if' => __('Sila masukkan nombor telefon pengadu.'),
            'cmp_reporter_phone.regex' => __('Sila masukkan nombor telefon yang sah.'),
            'cmp_reporter_email.required' => __('Sila masukkan alamat emel untuk menerima makluman status aduan.'),
            'cmp_reporter_email.email' => __('Sila masukkan alamat emel yang sah.'),
            'cmp_reporter_email.max' => __('Alamat emel tidak boleh melebihi 150 aksara.'),
        ];
    }
}