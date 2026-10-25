<?php

namespace App\Support;

use Illuminate\Http\Request;

class Susunan
{
    public static function dapatkan(
        Request $request,
        array $kolumDibenarkan,
        string $lalai,
        string $arahLalai = 'desc'
    ): array {
        $sort = (string) $request->input('sort', $lalai);
        $arah = strtolower((string) $request->input('direction', $arahLalai));

        if (! array_key_exists($sort, $kolumDibenarkan)) {
            $sort = $lalai;
        }

        if (! in_array($arah, ['asc', 'desc'], true)) {
            $arah = $arahLalai;
        }

        return [$kolumDibenarkan[$sort], $arah];
    }
}