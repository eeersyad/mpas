<?php

namespace Database\Seeders;

use App\Models\TetapanPenyelenggaraan;
use Illuminate\Database\Seeder;

class TetapanPenyelenggaraanSeeder extends Seeder
{
    public function run(): void
    {
        TetapanPenyelenggaraan::query()->firstOrCreate([], [
            'tpn_peranan_lihat' => ['BPH'],
            'tpn_peranan_isi' => ['BPH'],
        ]);
    }
}