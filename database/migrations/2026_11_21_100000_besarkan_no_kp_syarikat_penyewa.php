<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * No. KP / No. Syarikat penyewa: 20 aksara tidak cukup untuk no. pendaftaran syarikat
 * format baharu SSM (cth. "202001012345 (1234567-A)").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asb_tenant', function (Blueprint $table) {
            $table->string('tnt_ic_no', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('asb_tenant', function (Blueprint $table) {
            $table->string('tnt_ic_no', 20)->nullable()->change();
        });
    }
};
