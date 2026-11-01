<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        $pengguna = $request->user();

        return view('profil.kemaskini', compact('pengguna'));
    }

    public function update(Request $request): RedirectResponse
    {
        $pengguna = $request->user();

        $data = $request->validate(
            [
                'usr_name' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('users', 'usr_name')->ignore($pengguna->usr_id, 'usr_id'),
                ],
                'usr_password' => ['nullable', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
                'kata_laluan_semasa' => ['nullable', 'required_with:usr_password', 'current_password'],
            ],
            [
                'usr_name.required' => 'Sila masukkan nama pengguna.',
                'usr_name.unique' => 'Nama pengguna ini telah digunakan.',
                'usr_password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'usr_password.password.mixed' => 'Kata laluan mesti mengandungi huruf besar dan huruf kecil.',
                'usr_password.password.numbers' => 'Kata laluan mesti mengandungi sekurang-kurangnya satu nombor.',
                'usr_password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
                'kata_laluan_semasa.required_with' => 'Sila masukkan kata laluan semasa untuk menukar kata laluan.',
                'kata_laluan_semasa.current_password' => 'Kata laluan semasa tidak betul.',
            ]
        );

        $maklumat = [
            'usr_name' => $data['usr_name'],
            'updated_by' => $pengguna->usr_id,
        ];

        if (! empty($data['usr_password'])) {
            $maklumat['usr_password'] = $data['usr_password'];
        }

        $pengguna->update($maklumat);

        return redirect()
            ->route('profil.edit')
            ->with('berjaya', 'Profil anda berjaya dikemaskini.');
    }
}