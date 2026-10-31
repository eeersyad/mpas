<?php

namespace Database\Seeders;

use App\Models\KomponenPenyelenggaraan;
use Illuminate\Database\Seeder;

class KomponenPenyelenggaraanSeeder extends Seeder
{
    public function run(): void
    {
        $senarai = [
            [
                'nama' => 'Struktur dan Luaran Bangunan',
                'kod' => 'k1',
                'item' => [
                    'Dinding, Tiang & Rasuk',
                    'Bumbung, Siling & Gutter',
                    'Cat Luaran & Kemasan Dinding',
                    'Pintu, Tingkap, Kaca & Lockset',
                    'Pagar, Pintu Gerbang & Kawasan Perimeter',
                ],
            ],
            [
                'nama' => 'Sistem Mekanikal dan Elektrikal',
                'kod' => 'k2',
                'item' => [
                    'Papan Agihan Elektrik & Pemutus Litar',
                    'Lampu & Kipas',
                    'Suis, Soket Plug & Pendawaian',
                    'Sistem Penghawa Dingin',
                    'Alat Pemadam Api & Sistem Pencegah Kebakaran',
                ],
            ],
            [
                'nama' => 'Sistem Sanitasi, Plumbing dan Perparitan',
                'kod' => 'k3',
                'item' => [
                    'Paip Bekalan Air & Tangki Air',
                    'Mangkuk Tandas, Sink, Sinki & Paip Saluran',
                    'Sistem Kumbahan & Tangki Septik / Manhole',
                ],
            ],
        ];

        foreach ($senarai as $turutan => $komponen) {
            $items = [];

            foreach ($komponen['item'] as $index => $namaItem) {
                $items[] = [
                    'id' => $komponen['kod'] . 'i' . ($index + 1), // k1i1, k1i2, ...
                    'nama' => $namaItem,
                    'status' => 'ACTIVE',
                ];
            }

            // updateOrCreate: selamat dijalankan lebih sekali (tiada data berganda)
            KomponenPenyelenggaraan::updateOrCreate(
                ['amc_name' => $komponen['nama']],
                [
                    'amc_items' => $items,
                    'amc_order' => $turutan + 1,
                    'amc_status' => 'ACTIVE',
                ]
            );
        }
    }
}