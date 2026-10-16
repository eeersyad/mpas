<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_image', function (Blueprint $table) {
            $table->bigIncrements('cmpimg_id');

            $table->foreignId('cmp_id')
                ->constrained('complaint', 'cmp_id')
                ->restrictOnDelete();

            $table->enum('cmpimg_position', [
                'KIRI',
                'KANAN',
                'DEPAN',
                'BELAKANG',
            ]);

            $table->string('cmpimg_name');
            $table->string('cmpimg_path');
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_image');
    }
};