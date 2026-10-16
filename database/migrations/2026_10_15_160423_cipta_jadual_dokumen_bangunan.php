<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asb_document', function (Blueprint $table) {
            $table->bigIncrements('asbdoc_id');

            $table->foreignId('asb_id')
                ->constrained('assets_building', 'asb_id')
                ->restrictOnDelete();

            $table->string('asbdoc_type');
            $table->string('asbdoc_name');
            $table->string('asbdoc_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asb_document');
    }
};