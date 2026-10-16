<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KemaskiniAduanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->mempunyaiPeranan(['SYSTEM', 'KJ']) ?? false;
    }

    public function rules(): array
    {
        return [
            'cmp_status' => [
                'required',
                'in:DALAM_PENYELENGGARAAN,SELESAI',
            ],
            'cmp_maintenance_date' => ['nullable', 'date'],
            'cmp_completion_note' => [
                'required_if:cmp_status,SELESAI',
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cmp_status.required' =>
                'Sila pilih status penyelenggaraan.',
            'cmp_completion_note.required_if' =>
                'Sila masukkan catatan kerja apabila aduan diselesaikan.',
        ];
    }
}