<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Akses penyelenggaraan kini ditetapkan IKUT PENGGUNA (bukan ikut peranan).
 * Pengguna baharu tidak mendapat akses secara automatik sehingga ditambah di Tetapan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_akses_penyelenggaraan', function (Blueprint $table) {
            $table->id('tap_id');
            $table->unsignedBigInteger('usr_id')->unique();
            $table->enum('tap_tahap', ['LIHAT', 'ISI'])->default('LIHAT');
            $table->enum('tap_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        $this->pindahDaripadaPeranan();
    }

    /**
     * Kekalkan akses sedia ada: pengguna dalam peranan yang dibenarkan sebelum ini
     * dimasukkan sebagai pengguna berakses.
     */
    private function pindahDaripadaPeranan(): void
    {
        if (! Schema::hasTable('tetapan_penyelenggaraan')) {
            return;
        }

        $tetapan = DB::table('tetapan_penyelenggaraan')->first();
        $perananIsi = json_decode($tetapan->tpn_peranan_isi ?? '["BPH"]', true) ?: [];
        $perananLihat = json_decode($tetapan->tpn_peranan_lihat ?? '["BPH"]', true) ?: [];

        $pengguna = DB::table('users')
            ->where('usr_level', '!=', 'SYSTEM')
            ->whereIn('usr_level', array_unique(array_merge($perananIsi, $perananLihat)))
            ->get(['usr_id', 'usr_level', 'usr_status']);

        foreach ($pengguna as $p) {
            DB::table('tetapan_akses_penyelenggaraan')->insert([
                'usr_id' => $p->usr_id,
                'tap_tahap' => in_array($p->usr_level, $perananIsi, true) ? 'ISI' : 'LIHAT',
                'tap_status' => $p->usr_status === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tetapan_akses_penyelenggaraan');
    }
};
