<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE assets_land
            CHANGE asl_hakmilik_type asl_status_tanah
            ENUM('SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK')
            NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE assets_land
            CHANGE asl_status_tanah asl_hakmilik_type
            ENUM('SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEKAL')
            NULL
        ");
    }
};