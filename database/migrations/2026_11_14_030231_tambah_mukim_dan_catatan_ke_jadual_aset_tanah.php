<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets_land', function (Blueprint $table) {
            $table->enum('asl_mukim', [
                'ALOR_LIMBAT',
                'BUKIT_PAYUNG',
                'JERUNG',
                'MERCHANG',
                'PULAU_KERENGGA',
                'RUSILA',
            ])->nullable()->after('asl_location');

            $table->text('asl_note')->nullable()->after('asl_mukim');
        });
    }

    public function down(): void
    {
        Schema::table('assets_land', function (Blueprint $table) {
            $table->dropColumn(['asl_mukim', 'asl_note']);
        });
    }
};