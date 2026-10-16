<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asb_tenant', function (Blueprint $table) {
            $table->bigIncrements('tnt_id');

            $table->foreignId('asb_id')
                ->constrained('assets_building', 'asb_id')
                ->restrictOnDelete();

            $table->string('tnt_name');
            $table->string('tnt_acc_no')->nullable();
            $table->string('tnt_ic_no', 20)->nullable();
            $table->date('tnt_start_date')->nullable();
            $table->date('tnt_end_date')->nullable();

            $table->enum('tnt_status', [
                'AKTIF',
                'TIDAK_AKTIF',
            ])->default('AKTIF');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asb_tenant');
    }
};