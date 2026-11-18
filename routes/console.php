<?php

use Illuminate\Foundation\Inspiring;
use App\Models\Bangunan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tamatkan iklan bangunan yang sudah melepasi Tarikh Tamat Iklan
Artisan::command('aset:tamat-iklan', function () {
    $bilangan = Bangunan::query()
        ->where('asb_advertise', true)
        ->whereNotNull('asb_advertise_end')
        ->whereDate('asb_advertise_end', '<', today())
        ->update(['asb_advertise' => false]);

    $this->info("{$bilangan} iklan aset bangunan telah ditamatkan.");
})->purpose('Tukar Status Pengiklanan kepada Tidak bagi iklan yang telah tamat tempoh');

Schedule::command('aset:tamat-iklan')->dailyAt('00:05');
