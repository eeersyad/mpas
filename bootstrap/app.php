<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'peranan' => \App\Http\Middleware\PerananMiddleware::class,
        ]);

        $middleware->append(\App\Http\Middleware\HeaderKeselamatan::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Gantikan halaman "419 Page Expired" dengan mesej yang mesra pengguna
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'mesej' => 'Sesi anda telah tamat. Sila log masuk semula.',
                ], 419);
            }

            // Halaman log masuk dibiarkan terlalu lama
            if ($request->routeIs('authenticate')) {
                return redirect()
                    ->route('login')
                    ->withInput($request->only('usr_name'))
                    ->with('sesi_tamat', 'Halaman log masuk telah tamat tempoh. Sila cuba sekali lagi.');
            }

            // Masih log masuk: borang lama dihantar, pulangkan dengan input yang diisi
            if (auth()->check()) {
                return back()
                    ->withInput($request->except([
                        '_token',
                        'usr_password',
                        'usr_password_confirmation',
                        'kata_laluan_semasa',
                    ]))
                    ->with('ralat', 'Borang telah tamat tempoh. Sila semak dan hantar semula.');
            }

            // Sesi sudah tamat
            return redirect()
                ->route('login')
                ->with('sesi_tamat', 'Sesi anda telah tamat. Sila log masuk semula.');
        });
    })->create();
