<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tukar teks bebas lama kepada nilai enum
        DB::table('assets_land')
            ->whereNotNull('asl_status_cukaiT')
            ->orderBy('asl_id')
            ->each(function ($tanah) {
                $teks = mb_strtolower(trim((string) $tanah->asl_status_cukaiT));

                $nilai = match (true) {
                    str_contains($teks, 'belum'), str_contains($teks, 'tunggak') => 'BELUM_DIBAYAR',
                    str_contains($teks, 'bayar'), str_contains($teks, 'jelas') => 'TELAH_DIBAYAR',
                    default => null,
                };

                DB::table('assets_land')
                    ->where('asl_id', $tanah->asl_id)
                    ->update(['asl_status_cukaiT' => $nilai]);
            });

        // 2. Tukar jenis kolum kepada enum
        DB::statement("ALTER TABLE assets_land MODIFY asl_status_cukaiT ENUM('TELAH_DIBAYAR','BELUM_DIBAYAR') NULL");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE assets_land MODIFY asl_status_cukaiT VARCHAR(100) NULL');
    }
};