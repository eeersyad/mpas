<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HeaderKeselamatan
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Halang MPAS dimuatkan dalam iframe laman lain (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Halang pelayar meneka jenis fail
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Hadkan maklumat alamat yang dihantar ke laman luar
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Matikan ciri pelayar yang tidak digunakan
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // Paksa HTTPS (hanya bila sudah guna HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}