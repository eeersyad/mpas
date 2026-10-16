<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint', function (Blueprint $table) {
            $table->bigIncrements('cmp_id');

            $table->foreignId('asb_id')
                ->constrained('assets_building', 'asb_id')
                ->restrictOnDelete();

            $table->foreignId('tnt_id')
                ->nullable()
                ->constrained('asb_tenant', 'tnt_id')
                ->restrictOnDelete();

            $table->timestamp('cmp_received_date')->useCurrent();
            $table->text('cmp_note');
            $table->string('cmp_damage_type')->nullable();

            $table->enum('cmp_status', [
                'BARU',
                'DITERIMA',
                'DITOLAK',
                'DALAM_PENYELENGGARAAN',
                'SELESAI',
            ])->default('BARU');

            $table->foreignId('cmp_accepted_by')
                ->nullable()
                ->constrained('users', 'usr_id')
                ->nullOnDelete();

            $table->timestamp('cmp_accepted_at')->nullable();
            $table->date('cmp_maintenance_date')->nullable();

            $table->foreignId('cmp_maintained_by')
                ->nullable()
                ->constrained('users', 'usr_id')
                ->nullOnDelete();

            $table->text('cmp_completion_note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint');
    }
};