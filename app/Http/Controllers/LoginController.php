<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function login(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('akses.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'usr_name' => ['required', 'string', 'max:100'],
                'password' => ['required', 'string'],
            ],
            [
                'usr_name.required' => 'Sila masukkan nama pengguna.',
                'password.required' => 'Sila masukkan kata laluan.',
            ]
        );

        $authenticated = Auth::attempt([
            'usr_name' => $data['usr_name'],
            'password' => $data['password'],
            'usr_status' => 'ACTIVE',
        ]);

        if (! $authenticated) {
            return back()
                ->withInput($request->only('usr_name'))
                ->withErrors([
                    'usr_name' => 'Nama pengguna atau kata laluan tidak sah.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('berjaya', 'Log masuk berjaya.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}