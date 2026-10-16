<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->timestamp('cmp_completed_at')
                ->nullable()
                ->after('cmp_completion_note');
        });
    }

    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->dropColumn('cmp_completed_at');
        });
    }
};