<?php

namespace App\Support;

final class Peranan
{
    /**
     * Nama penuh bagi setiap peranan pengguna MPAS.
     */
    public const NAMA = [
        'SYSTEM' => 'Super Admin',
        'BPH' => 'Bahagian Pengurusan Harta',
        'BP' => 'Bahagian Perancang',
        'KJ' => 'Jabatan Kejuruteraan',
    ];

    public static function nama(?string $kod): string
    {
        return self::NAMA[$kod] ?? (string) $kod;
    }
}
