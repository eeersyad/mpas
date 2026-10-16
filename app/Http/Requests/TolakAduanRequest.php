<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TolakAduanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->mempunyaiPeranan(['SYSTEM', 'BPH']) ?? false;
    }

    public function rules(): array
    {
        return [
            'cmp_completion_note' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cmp_completion_note.required' =>
                'Sila masukkan sebab penolakan aduan.',
        ];
    }
}