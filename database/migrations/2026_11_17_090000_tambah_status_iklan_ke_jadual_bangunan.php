<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            // Ya = aset diiklankan untuk disewa di Portal Awam
            $table->boolean('asb_advertise')
                ->default(false)
                ->after('asb_premise_status');
        });
    }

    public function down(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->dropColumn('asb_advertise');
        });
    }
};
