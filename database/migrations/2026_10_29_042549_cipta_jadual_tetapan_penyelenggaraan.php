<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_penyelenggaraan', function (Blueprint $table) {
            $table->bigIncrements('tpn_id');

            // Contoh: ["BPH","KJ"]
            $table->json('tpn_peranan_lihat');
            $table->json('tpn_peranan_isi');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tetapan_penyelenggaraan');
    }
};