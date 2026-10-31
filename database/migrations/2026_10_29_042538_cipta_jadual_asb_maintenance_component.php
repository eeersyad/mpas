<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asb_maintenance_component', function (Blueprint $table) {
            $table->bigIncrements('amc_id');

            $table->string('amc_name');

            // Senarai item: [{"id":"k1i1","nama":"...","status":"ACTIVE"}, ...]
            $table->json('amc_items');

            $table->unsignedInteger('amc_order')->default(0);

            $table->enum('amc_status', [
                'ACTIVE',
                'INACTIVE',
            ])->default('ACTIVE');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asb_maintenance_component');
    }
};