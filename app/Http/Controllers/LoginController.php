<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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
        
        $kunciHad = 'login:' . Str::lower($data['usr_name']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($kunciHad, 5)) {
            $saat = RateLimiter::availableIn($kunciHad);

            return back()
                ->withInput($request->only('usr_name'))
                ->withErrors([
                    'usr_name' => "Terlalu banyak cubaan. Sila cuba semula dalam {$saat} saat.",
                ]);
        }

        $authenticated = Auth::attempt([
            'usr_name' => $data['usr_name'],
            'password' => $data['password'],
            'usr_status' => 'ACTIVE',
        ]);

        if (! $authenticated) {
            RateLimiter::hit($kunciHad, 300);

            return back()
                ->withInput($request->only('usr_name'))
                ->withErrors([
                    'usr_name' => 'Nama pengguna atau kata laluan tidak sah.',
                ]);
        }
        RateLimiter::clear($kunciHad);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('berjaya', 'Log masuk berjaya.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $sesiTamat = $request->input('sebab') === 'tamat';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($sesiTamat) {
            return redirect()
                ->route('login')
                ->with('sesi_tamat', 'Sesi anda telah tamat kerana tiada aktiviti. Sila log masuk semula.');
        }

        return redirect()->route('login');
    }

    /**
     * Panjangkan sesi (dipanggil oleh amaran sesi atau bila pengguna aktif).
     */
    public function kekalSesi(): Response
    {
        return response()->noContent();
    }
}