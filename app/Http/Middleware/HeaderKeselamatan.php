<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class HeaderKeselamatan
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Jangan dedahkan versi PHP (header X-Powered-By)
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }
        $response->headers->remove('X-Powered-By');

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

        // Content Security Policy: hanya untuk halaman HTML, dan tidak semasa "npm run dev"
        $jenis = (string) $response->headers->get('Content-Type');
        $halamanHtml = $jenis === '' || str_contains($jenis, 'text/html');

        // Halaman dalaman tidak boleh disimpan oleh pelayar. Selepas log keluar,
        // butang "Back" akan meminta semula halaman daripada pelayan (dan dihantar ke log masuk)
        if ($halamanHtml && $request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        if ($halamanHtml && config('keselamatan.csp', true) && ! Vite::isRunningHot()) {
            $response->headers->set('Content-Security-Policy', $this->csp());
        }

        return $response;
    }

    /**
     * Senarai sumber yang dibenarkan. Tambah di sini jika menggunakan CDN / perkhidmatan baharu.
     */
    private function csp(): string
    {
        $cdn = 'https://cdn.jsdelivr.net https://unpkg.com';
        $cloudflare = 'https://static.cloudflareinsights.com';
        $petaTile = 'https://*.tile.openstreetmap.org https://server.arcgisonline.com';

        // Reverb (notifikasi masa nyata)
        $reverb = '';
        $hos = config('broadcasting.connections.reverb.options.host');
        if ($hos) {
            $port = config('broadcasting.connections.reverb.options.port');
            $alamat = $hos . ($port ? ':' . $port : '');
            // pusher-js mencuba ws:// dan wss://, jadi benarkan kedua-duanya
            $reverb = 'ws://' . $alamat . ' wss://' . $alamat;
        }

        $arahan = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' {$cdn} {$cloudflare}",
            "style-src 'self' 'unsafe-inline' {$cdn}",
            "font-src 'self' data: {$cdn}",
            "img-src 'self' data: blob: {$cdn} {$petaTile}",
            "connect-src 'self' {$cdn} https://cloudflareinsights.com {$reverb}",
            "frame-src 'self'",
            "worker-src 'self'",
            "manifest-src 'self'",
            "media-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        return implode('; ', array_map('trim', $arahan));
    }
}
