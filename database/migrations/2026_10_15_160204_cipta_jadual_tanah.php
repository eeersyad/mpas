<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets_land', function (Blueprint $table) {
            $table->bigIncrements('asl_id');

            $table->foreignId('ast_id')
                ->constrained('assets', 'ast_id')
                ->restrictOnDelete();

            $table->string('asl_ref_no')->unique();
            $table->string('asl_lot_no')->nullable();
            $table->string('asl_location')->nullable();
            $table->longText('asl_gmaps_location')->nullable();
            $table->decimal('asl_latitude', 10, 7)->nullable();
            $table->decimal('asl_longitude', 10, 7)->nullable();
            $table->date('asl_tarikh_terima_hakmilik')->nullable();
            $table->date('asl_tarikh_serah_balik_hakmilik')->nullable();

            $table->enum('asl_status_hakmilik', [
                'HAKMILIK',
                'RIZAB',
                'STRATA',
                'LAIN_LAIN',
            ])->nullable();

            $table->decimal('asl_land_size', 12, 2)->nullable();
            $table->string('asl_hakmilik_no')->nullable();

            $table->enum('asl_hakmilik_type', [
                'SEWA',
                'TIDAK_DISEWA',
                'PAJAK',
                'TIDAK_DIPAJAK',
                'KEKAL',
            ])->nullable();

            $table->decimal('asl_land_price', 15, 2)->nullable();
            $table->decimal('asl_land_skp', 15, 2)->nullable();
            $table->decimal('asl_cukaiT', 15, 2)->nullable();
            $table->string('asl_status_cukaiT')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets_land');
    }
};