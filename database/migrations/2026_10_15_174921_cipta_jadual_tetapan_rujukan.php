<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_rujukan', function (Blueprint $table) {
            $table->bigIncrements('ruj_id');
            $table->enum('ruj_jenis', ['TANAH', 'BANGUNAN'])->unique();
            $table->string('ruj_awalan', 50);
            $table->unsignedBigInteger('ruj_nombor_mula')->default(1);
            $table->unsignedBigInteger('ruj_nombor_semasa')->nullable();
            $table->unsignedTinyInteger('ruj_panjang_nombor')->default(6);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tetapan_rujukan');
    }
};