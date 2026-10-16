<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['usr_name' => 'system'],
            [
                'usr_password' => 'system@mdm2026',
                'usr_level' => 'SYSTEM',
                'usr_status' => 'ACTIVE',
            ]
        );
    }
}