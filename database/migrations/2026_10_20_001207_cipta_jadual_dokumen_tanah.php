<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asl_document', function (Blueprint $table) {
            $table->bigIncrements('asldoc_id');

            $table->foreignId('asl_id')
                ->constrained('assets_land', 'asl_id')
                ->restrictOnDelete();

            $table->string('asldoc_type');
            $table->string('asldoc_name');
            $table->string('asldoc_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asl_document');
    }
};