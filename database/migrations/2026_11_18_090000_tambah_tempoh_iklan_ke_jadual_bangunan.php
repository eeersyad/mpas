<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->date('asb_advertise_start')->nullable()->after('asb_advertise');
            $table->date('asb_advertise_end')->nullable()->after('asb_advertise_start');
        });
    }

    public function down(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->dropColumn(['asb_advertise_start', 'asb_advertise_end']);
        });
    }
};
