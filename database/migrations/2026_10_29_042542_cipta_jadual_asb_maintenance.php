<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asb_maintenance', function (Blueprint $table) {
            $table->bigIncrements('asm_id');

            $table->foreignId('asb_id')
                ->constrained('assets_building', 'asb_id')
                ->restrictOnDelete();

            $table->date('asm_inspection_date');

            $table->foreignId('asm_inspected_by')
                ->constrained('users', 'usr_id')
                ->restrictOnDelete();

            // Nilai pemeriksaan:
            // [{"amc_id":1,"nama":"...","item":[{"id":"k1i1","nama":"...","nilai":"BAIK"}]}]
            $table->json('asm_items');

            $table->text('asm_note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['asb_id', 'asm_inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asb_maintenance');
    }
};