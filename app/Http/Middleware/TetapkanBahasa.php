<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bahasa halaman awam aduan (BM / EN). Lalai: Bahasa Melayu.
 * Pilihan disimpan dalam session melalui laluan "bahasa".
 */
class TetapkanBahasa
{
    public const BAHASA = ['ms', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $bahasa = $request->session()->get('bahasa', 'ms');

        app()->setLocale(in_array($bahasa, self::BAHASA, true) ? $bahasa : 'ms');

        return $next($request);
    }
}
