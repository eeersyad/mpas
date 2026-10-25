<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->string('cmp_reporter_name')
                ->nullable()
                ->after('tnt_id');
        });
    }

    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->dropColumn('cmp_reporter_name');
        });
    }
};