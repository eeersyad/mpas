<?php

namespace App\Services;

use App\Models\Bangunan;
use App\Models\KodBangunan;
use Illuminate\Support\Str;

class KodQr
{
    public function jana(Bangunan $bangunan, int $userId): KodBangunan
    {
        $kodSediaAda = $bangunan->kod()->first();

        if ($kodSediaAda) {
            return $kodSediaAda;
        }

        do {
            $nilaiKod = 'MPAS-' . strtoupper(Str::random(16));
        } while (KodBangunan::query()
            ->where('qr_code_value', $nilaiKod)
            ->exists());

        return $bangunan->kod()->create([
            'qr_code_value' => $nilaiKod,
            'qr_status' => 'ACTIVE',
            'generated_by' => $userId,
        ]);
    }
}