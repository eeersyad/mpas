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

            'cmp_damage_type' => ['required', 'string', 'max:255'],
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
            'jenis_pengadu.required' => 'Sila pilih kategori pengadu.',
            'tnt_id.required_if' => 'Sila pilih nama penyewa.',
            'cmp_damage_type.required' => 'Sila pilih jenis kerosakan.',
            'cmp_note.required' => 'Sila masukkan keterangan aduan.',
            'gambar.max' => 'Maksimum empat gambar boleh dimuat naik.',
            'gambar.*.max' => 'Saiz setiap gambar tidak boleh melebihi 5 MB.',
            'cmp_reporter_name.required_if' => 'Sila masukkan nama pengadu.',
        ];
    }
}