<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Jana kod QR (SVG) dengan logo MPAS di tengah.
 * Pembetulan ralat tahap H (~30%) supaya QR masih boleh diimbas walaupun tengahnya ditutup logo.
 */
final class KodQr
{
    private const LOGO = 'images/logo-qr.png';

    // Lebar logo berbanding lebar QR (kekal di bawah had selamat tahap H)
    private const NISBAH_LOGO = 0.2;

    private static ?string $logoBase64 = null;

    // Tukar nombor ini jika reka bentuk QR diubah, supaya cache lama tidak digunakan
    private const VERSI = 2;

    /**
     * QR disimpan dalam cache: dijana sekali sahaja bagi setiap pautan dan saiz.
     */
    public static function svg(string $pautan, int $saiz = 260, int $margin = 1): string
    {
        $kunci = 'kodqr:v' . self::VERSI . ':' . md5($pautan . '|' . $saiz . '|' . $margin);

        return Cache::rememberForever($kunci, fn () => self::jana($pautan, $saiz, $margin));
    }

    private static function jana(string $pautan, int $saiz, int $margin): string
    {
        $svg = (string) QrCode::format('svg')
            ->size($saiz)
            ->margin($margin)
            ->errorCorrection('H')
            ->style('dot', 0.85)
            ->eye('circle')
            ->color(15, 107, 97)
            ->generate($pautan);

        $logo = self::logo();

        if ($logo === null) {
            return $svg;
        }

        // Ukuran sebenar kanvas SVG (viewBox), jika ada
        $lebar = $saiz;
        if (preg_match('/viewBox="\s*[\d.]+\s+[\d.]+\s+([\d.]+)\s+([\d.]+)\s*"/', $svg, $padanan)) {
            $lebar = (float) $padanan[1];
        }

        // Logo dalam bulatan putih dengan ruang lega di sekelilingnya
        $tengah = $lebar / 2;
        $kotak = $lebar * self::NISBAH_LOGO;
        $jejari = $kotak * 0.78;
        $xLogo = $tengah - ($kotak / 2);

        $tindih = sprintf(
            '<circle cx="%1$.2f" cy="%1$.2f" r="%2$.2f" fill="#ffffff"/>'
            . '<circle cx="%1$.2f" cy="%1$.2f" r="%3$.2f" fill="none" stroke="#0f6b61" stroke-opacity=".15" stroke-width="%4$.2f"/>'
            . '<image x="%5$.2f" y="%5$.2f" width="%6$.2f" height="%6$.2f" preserveAspectRatio="xMidYMid meet" href="%7$s" xlink:href="%7$s"/>',
            $tengah, $jejari, $jejari - ($lebar * 0.004), $lebar * 0.006, $xLogo, $kotak, $logo
        );

        // Pastikan namespace xlink wujud untuk pelayar/pencetak lama
        if (! str_contains($svg, 'xmlns:xlink')) {
            $svg = preg_replace('/<svg\b/', '<svg xmlns:xlink="http://www.w3.org/1999/xlink"', $svg, 1);
        }

        return preg_replace('/<\/svg>\s*$/', $tindih . '</svg>', $svg, 1) ?? $svg;
    }

    private static function logo(): ?string
    {
        if (self::$logoBase64 !== null) {
            return self::$logoBase64;
        }

        $fail = public_path(self::LOGO);

        if (! is_file($fail)) {
            return null;
        }

        return self::$logoBase64 = 'data:image/png;base64,' . base64_encode((string) file_get_contents($fail));
    }
}
