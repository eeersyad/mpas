<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tetapan_pegawai', function (Blueprint $table) {
            $table->id('pgw_id');
            $table->string('pgw_nama', 255);
            $table->string('pgw_jawatan', 255);
            $table->enum('pgw_peranan', ['PENGUMPUL', 'PENGESAH', 'KEDUA_DUA']);
            $table->enum('pgw_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('assets_building', function (Blueprint $table) {
            $table->unsignedBigInteger('asb_collector_id')->nullable()->after('asb_collector_position');
            $table->unsignedBigInteger('asb_verifier_id')->nullable()->after('asb_verifier_position');
        });

        $this->pindahDataLama();
    }

    /**
     * Masukkan nama pegawai yang sudah ada dalam rekod bangunan ke table baharu,
     * kemudian pautkan rekod bangunan kepada pegawai tersebut.
     */
    private function pindahDataLama(): void
    {
        $pegawai = [];

        $kunci = fn ($nama, $jawatan) => mb_strtolower(trim((string) $nama)) . '|' . mb_strtolower(trim((string) $jawatan));

        foreach (DB::table('assets_building')->get() as $bangunan) {
            foreach (['collector' => 'PENGUMPUL', 'verifier' => 'PENGESAH'] as $jenis => $peranan) {
                $nama = trim((string) $bangunan->{"asb_{$jenis}_name"});

                if ($nama === '') {
                    continue;
                }

                $jawatan = trim((string) $bangunan->{"asb_{$jenis}_position"});
                $k = $kunci($nama, $jawatan);

                if (! isset($pegawai[$k])) {
                    $pegawai[$k] = ['nama' => $nama, 'jawatan' => $jawatan ?: '-', 'peranan' => $peranan];
                } elseif ($pegawai[$k]['peranan'] !== $peranan) {
                    $pegawai[$k]['peranan'] = 'KEDUA_DUA';
                }
            }
        }

        foreach ($pegawai as $k => $p) {
            $pegawai[$k]['id'] = DB::table('tetapan_pegawai')->insertGetId([
                'pgw_nama' => $p['nama'],
                'pgw_jawatan' => $p['jawatan'],
                'pgw_peranan' => $p['peranan'],
                'pgw_status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (DB::table('assets_building')->get() as $bangunan) {
            $kemaskini = [];

            foreach (['collector', 'verifier'] as $jenis) {
                $k = $kunci($bangunan->{"asb_{$jenis}_name"}, $bangunan->{"asb_{$jenis}_position"});

                if (trim((string) $bangunan->{"asb_{$jenis}_name"}) !== '' && isset($pegawai[$k])) {
                    $kemaskini["asb_{$jenis}_id"] = $pegawai[$k]['id'];
                }
            }

            if ($kemaskini) {
                DB::table('assets_building')->where('asb_id', $bangunan->asb_id)->update($kemaskini);
            }
        }
    }

    public function down(): void
    {
        Schema::table('assets_building', function (Blueprint $table) {
            $table->dropColumn(['asb_collector_id', 'asb_verifier_id']);
        });

        Schema::dropIfExists('tetapan_pegawai');
    }
};