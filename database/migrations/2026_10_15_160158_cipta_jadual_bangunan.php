<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets_building', function (Blueprint $table) {
            $table->bigIncrements('asb_id');

            $table->foreignId('ast_id')
                ->constrained('assets', 'ast_id')
                ->restrictOnDelete();

            $table->string('asb_ref_no')->unique();
            $table->string('asb_name');
            $table->string('asb_add1')->nullable();
            $table->string('asb_add2')->nullable();
            $table->string('asb_postcode', 10)->nullable();
            $table->string('asb_distinct')->nullable();
            $table->string('asb_mukim')->nullable();
            $table->string('asb_city')->nullable();

            $table->enum('asb_category', [
                'PREMIS_PERNIAGAAN',
                'TANDAS',
                'DEWAN',
                'PONDOK_BAS',
            ])->nullable();

            $table->string('asb_sub_category')->nullable();
            $table->decimal('asb_size', 12, 2)->nullable();
            $table->string('asb_agency')->nullable();
            $table->string('asb_ministry')->nullable();
            $table->string('asb_dept')->nullable();
            $table->string('asb_state')->nullable();
            $table->string('asb_country')->nullable();
            $table->longText('asb_gmaps_location')->nullable();
            $table->decimal('asb_latitude', 10, 7)->nullable();
            $table->decimal('asb_longitude', 10, 7)->nullable();
            $table->boolean('asb_aset_warisan')->default(false);

            $table->decimal('asb_kos_siap_bina_asal', 15, 2)->nullable();
            $table->decimal('asb_kos_tambahan', 15, 2)->nullable();
            $table->decimal('asb_total_kos', 15, 2)->nullable();
            $table->date('asb_completion_date')->nullable();
            $table->string('asb_financing_source')->nullable();
            $table->string('asb_ptj_code')->nullable();
            $table->decimal('asb_current_value', 15, 2)->nullable();
            $table->date('asb_assessment_date')->nullable();

            $table->unsignedInteger('asb_total_block')->default(0);
            $table->unsignedInteger('asb_total_ext_structure')->default(0);
            $table->string('asb_tnb_acc_no')->nullable();
            $table->string('asb_satu_acc_no')->nullable();

            $table->enum('asb_maintenance_status', [
                'RENDAH',
                'SEDERHANA',
                'KRITIKAL',
            ])->default('RENDAH');

            $table->enum('asb_premise_status', [
                'SEWA',
                'TIDAK_DISEWA',
                'PAJAK',
                'TIDAK_DIPAJAK',
                'KEGUNAAN_MDM',
                'DILUPUS',
            ])->default('KEGUNAAN_MDM');

            $table->string('asb_collector_name')->nullable();
            $table->string('asb_collector_position')->nullable();
            $table->timestamp('asb_collected_at')->nullable();
            $table->string('asb_verifier_name')->nullable();
            $table->string('asb_verifier_position')->nullable();
            $table->timestamp('asb_verified_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets_building');
    }
};