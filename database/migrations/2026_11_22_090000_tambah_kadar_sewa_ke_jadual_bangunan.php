<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->decimal('asb_rental_rate', 12, 2)->nullable()->after('asb_maintenance_status');
        });
    }

    public function down(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->dropColumn('asb_rental_rate');
        });
    }
};
