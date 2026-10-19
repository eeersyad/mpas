<?php

namespace App\Support;

use Illuminate\Support\Str;

final class Paparan
{
    public static function label(?string $nilai): string
    {
        if (blank($nilai)) {
            return '-';
        }

        return match ($nilai) {
            'ACTIVE' => 'Aktif',
            'INACTIVE' => 'Tidak aktif',

            'HAKMILIK' => 'Hak milik',
            'RIZAB' => 'Rizab',
            'STRATA' => 'Strata',
            'LAIN_LAIN', 'LAIN-LAIN' => 'Lain-lain',

            'SEWA' => 'Sewa',
            'TIDAK_DISEWA' => 'Tidak disewa',
            'PAJAK' => 'Pajak',
            'TIDAK_DIPAJAK' => 'Tidak dipajak',
            'KEKAL' => 'Kekal',
            'KEGUNAAN_MDM' => 'Kegunaan MDM',
            'DILUPUS' => 'Dilupus',

            'RENDAH' => 'Rendah',
            'SEDERHANA' => 'Sederhana',
            'KRITIKAL' => 'Kritikal',

            'BARU' => 'Baru',
            'DITERIMA' => 'Diterima',
            'DITOLAK' => 'Ditolak',
            'DALAM_PENYELENGGARAAN' => 'Dalam penyelenggaraan',
            'SELESAI' => 'Selesai',

            default => Str::of($nilai)
                ->replace('_', ' ')
                ->lower()
                ->title()
                ->toString(),
        };
    }
}