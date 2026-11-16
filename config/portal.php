<?php

/*
|--------------------------------------------------------------------------
| Portal Awam MPAS
|--------------------------------------------------------------------------
| Tetapan halaman awam (one stop centre orang awam).
*/

return [

    // Maklumat hubungan yang dipaparkan kepada orang awam
    'hubungan' => [
        'nama' => env('PORTAL_NAMA', 'Bahagian Pengurusan Harta, Majlis Daerah Marang'),
        'telefon' => env('PORTAL_TELEFON', '09-618 2366'),
        'emel' => env('PORTAL_EMEL', 'mdmarang@terengganu.gov.my'),
        'alamat' => env('PORTAL_ALAMAT', 'Majlis Daerah Marang, 21600 Marang, Terengganu'),
    ],

    // Status yang dikira "tersedia untuk disewa"
    'status_tersedia' => ['TIDAK_DISEWA', 'TIDAK_DIPAJAK'],

    // Status penyelenggaraan bangunan yang TIDAK dipaparkan kepada umum
    'penyelenggaraan_dikecualikan' => ['KRITIKAL'],

];
