<?php

namespace App\Services;

use App\Models\TetapanRujukan;
use RuntimeException;

class Rujukan
{
    public function jana(string $jenis, ?int $userId = null): string
    {
        $tetapan = TetapanRujukan::query()
            ->where('ruj_jenis', $jenis)
            ->lockForUpdate()
            ->first();

        if (! $tetapan) {
            throw new RuntimeException(
                "Tetapan nombor rujukan {$jenis} belum dibuat."
            );
        }

        $nomborSeterusnya = $tetapan->ruj_nombor_semasa === null
            ? $tetapan->ruj_nombor_mula
            : $tetapan->ruj_nombor_semasa + 1;

        $nomborDiformat = str_pad(
            (string) $nomborSeterusnya,
            max($tetapan->ruj_panjang_nombor, strlen((string) $nomborSeterusnya)),
            '0',
            STR_PAD_LEFT
        );

        $tetapan->update([
            'ruj_nombor_semasa' => $nomborSeterusnya,
            'updated_by' => $userId,
        ]);

        return "{$tetapan->ruj_awalan}-{$nomborDiformat}";
    }
}