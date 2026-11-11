<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->string('cmp_ref_no', 20)->nullable()->unique()->after('cmp_id');
            $table->string('cmp_reporter_email', 150)->nullable()->after('cmp_reporter_phone');
        });

        // Beri no. rujukan kepada aduan sedia ada
        DB::table('complaint')->orderBy('cmp_id')->each(function ($aduan) {
            $tahun = $aduan->cmp_received_date
                ? date('Y', strtotime($aduan->cmp_received_date))
                : date('Y');

            DB::table('complaint')
                ->where('cmp_id', $aduan->cmp_id)
                ->update(['cmp_ref_no' => sprintf('ADU-%s-%05d', $tahun, $aduan->cmp_id)]);
        });
    }

    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->dropUnique(['cmp_ref_no']);
            $table->dropColumn(['cmp_ref_no', 'cmp_reporter_email']);
        });
    }
};
