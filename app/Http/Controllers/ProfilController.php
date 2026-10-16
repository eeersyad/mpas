<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
                'usr_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ],
            [
                'usr_name.required' => 'Sila masukkan nama pengguna.',
                'usr_name.unique' => 'Nama pengguna ini telah digunakan.',
                'usr_password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'usr_password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
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