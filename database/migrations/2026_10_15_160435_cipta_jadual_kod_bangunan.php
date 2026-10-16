<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets_building_code', function (Blueprint $table) {
            $table->bigIncrements('qr_id');

            $table->foreignId('asb_id')
                ->constrained('assets_building', 'asb_id')
                ->restrictOnDelete();

            $table->string('qr_code_value')->unique();
            $table->string('qr_image_path')->nullable();

            $table->enum('qr_status', [
                'ACTIVE',
                'INACTIVE',
            ])->default('ACTIVE');

            $table->timestamp('generated_at')->useCurrent();
            $table->unsignedBigInteger('generated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets_building_code');
    }
};