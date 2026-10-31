<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_penyewa', function (Blueprint $table) {
            $table->bigIncrements('tpy_id');
            $table->unsignedInteger('tpy_tempoh_hampir_tamat_perjanjian')
                ->default(30);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tetapan_penyewa');
    }
};
