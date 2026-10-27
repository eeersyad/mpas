<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->string('asb_no_file', 100)
                ->nullable()
                ->after('asb_name');
        });

        Schema::table('assets_land', function (Blueprint $table) {
            $table->string('asl_no_file', 100)
                ->nullable()
                ->after('asl_lot_no');
        });
    }

    public function down(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->dropColumn('asb_no_file');
        });

        Schema::table('assets_land', function (Blueprint $table) {
            $table->dropColumn('asl_no_file');
        });
    }
};
