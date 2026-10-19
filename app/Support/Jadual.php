<?php

namespace App\Support;

use Illuminate\Http\Request;

class Jadual
{
    public const HAD_DIBENARKAN = [15, 30, 50, 100];

    public static function had(Request $request): int
    {
        $had = (int) $request->input('had', 15);

        return in_array($had, self::HAD_DIBENARKAN, true)
            ? $had
            : 15;
    }
}