<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asb_tenant', function (Blueprint $table) {
            $table->renameColumn('tnt_start_date', 'tnt_mula_perjanjian');
            $table->renameColumn('tnt_end_date', 'tnt_tamat_perjanjian');
        });
    }

    public function down(): void
    {
        Schema::table('asb_tenant', function (Blueprint $table) {
            $table->renameColumn('tnt_mula_perjanjian', 'tnt_start_date');
            $table->renameColumn('tnt_tamat_perjanjian', 'tnt_end_date');
        });
    }
};
