<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_completion_image', function (Blueprint $table) {
            $table->bigIncrements('cmpcimg_id');

            $table->foreignId('cmp_id')
                ->constrained('complaint', 'cmp_id')
                ->restrictOnDelete();

            $table->string('cmpcimg_name');
            $table->string('cmpcimg_path');
            $table->unsignedTinyInteger('cmpcimg_order');

            $table->foreignId('created_by')
                ->constrained('users', 'usr_id')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_completion_image');
    }
};