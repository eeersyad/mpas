<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_aduan', function (Blueprint $table) {
            $table->bigIncrements('adu_id');

            $table->unsignedInteger('adu_tempoh_papar_selesai')
                ->default(30);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tetapan_aduan');
    }
};