<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asb_maintenance_image', function (Blueprint $table) {
            $table->id('asmi_id');
            $table->unsignedBigInteger('asm_id');
            $table->string('asmi_name', 150);
            $table->text('asmi_summary')->nullable();
            $table->string('asmi_path');
            $table->string('asmi_mime', 50)->nullable();
            $table->unsignedInteger('asmi_size')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index('asm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asb_maintenance_image');
    }
};
