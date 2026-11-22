<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Status premis (bangunan) dan status tanah kini diurus di Tetapan.
 * Lajur ENUM ditukar kepada VARCHAR supaya status baharu boleh ditambah tanpa ubah kod.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_status_aset', function (Blueprint $table) {
            $table->id('sts_id');
            $table->enum('sts_jenis', ['BANGUNAN', 'TANAH']);
            $table->string('sts_kod', 50);
            $table->string('sts_label', 100);
            $table->string('sts_warna', 20)->default('kelabu');
            $table->boolean('sts_boleh_iklan')->default(false);
            $table->unsignedInteger('sts_susunan')->default(0);
            $table->enum('sts_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['sts_jenis', 'sts_kod']);
        });

        // Status sedia ada (kod kekal sama supaya rekod lama tidak terjejas)
        $sedia = [
            'BANGUNAN' => [
                ['SEWA', 'Sewa', 'hijau', false],
                ['TIDAK_DISEWA', 'Tidak Disewa', 'merah', true],
                ['PAJAK', 'Pajak', 'ungu', false],
                ['TIDAK_DIPAJAK', 'Tidak Dipajak', 'kuning', false],
                ['KEGUNAAN_MDM', 'Kegunaan MDM', 'biru', false],
                ['DILUPUS', 'Dilupus', 'kelabu', false],
            ],
            'TANAH' => [
                ['SEWA', 'Sewa', 'hijau', false],
                ['TIDAK_DISEWA', 'Tidak Disewa', 'merah', false],
                ['PAJAK', 'Pajak', 'ungu', false],
                ['TIDAK_DIPAJAK', 'Tidak Dipajak', 'kuning', false],
            ],
        ];

        foreach ($sedia as $jenis => $senarai) {
            foreach ($senarai as $i => [$kod, $label, $warna, $iklan]) {
                DB::table('tetapan_status_aset')->insert([
                    'sts_jenis' => $jenis,
                    'sts_kod' => $kod,
                    'sts_label' => $label,
                    'sts_warna' => $warna,
                    'sts_boleh_iklan' => $iklan,
                    'sts_susunan' => $i + 1,
                    'sts_status' => 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ENUM → VARCHAR (nilai sedia ada kekal)
        DB::statement("ALTER TABLE assets_building MODIFY asb_premise_status VARCHAR(50) NULL DEFAULT 'KEGUNAAN_MDM'");
        DB::statement('ALTER TABLE assets_land MODIFY asl_status_tanah VARCHAR(50) NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE assets_building MODIFY asb_premise_status ENUM('SEWA','TIDAK_DISEWA','PAJAK','TIDAK_DIPAJAK','KEGUNAAN_MDM','DILUPUS') NULL DEFAULT 'KEGUNAAN_MDM'");
        DB::statement("ALTER TABLE assets_land MODIFY asl_status_tanah ENUM('SEWA','TIDAK_DISEWA','PAJAK','TIDAK_DIPAJAK') NULL");

        Schema::dropIfExists('tetapan_status_aset');
    }
};
