<?php

namespace App\Support;

final class MenuSidebar
{
    /**
     * Senarai menu sidebar (desktop dan telefon).
     * Untuk tambah menu baharu, tambah satu entri di sini sahaja.
     */
    public static function senarai(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'icon' => 'bi-grid-1x2-fill',
                'url' => route('dashboard'),
                'roles' => ['SYSTEM', 'BPH', 'BP', 'KJ'],
                'active' => request()->routeIs('dashboard'),
            ],
            [
                'label' => 'Aset Bangunan',
                'icon' => 'bi-buildings-fill',
                'url' => route('bangunan.index'),
                'roles' => ['SYSTEM', 'BPH'],
                'active' => request()->routeIs('bangunan.*'),
            ],
            [
                'label' => 'Aset Tanah',
                'icon' => 'bi-map-fill',
                'url' => route('tanah.index'),
                'roles' => ['SYSTEM', 'BPH', 'BP'],
                'active' => request()->routeIs('tanah.*'),
            ],
            [
                'label' => 'Aduan',
                'icon' => 'bi-clipboard2-pulse-fill',
                'url' => route('aduan.index'),
                'roles' => ['SYSTEM', 'BPH', 'KJ'],
                'active' => request()->routeIs('aduan.*'),
            ],
            [
                'label' => 'Pengguna',
                'icon' => 'bi-people-fill',
                'url' => route('pengguna.index'),
                'roles' => ['SYSTEM'],
                'active' => request()->routeIs('pengguna.*'),
            ],
            [
                'label' => 'Kod QR',
                'icon' => 'bi-qr-code',
                'url' => route('kodqr.index'),
                'roles' => ['SYSTEM', 'BPH'],
                'active' => request()->routeIs('kodqr.*'),
            ],
            [
                'label' => 'Laporan',
                'icon' => 'bi-bar-chart-line-fill',
                'url' => route('laporan.index'),
                'roles' => ['SYSTEM', 'BPH', 'BP', 'KJ'],
                'active' => request()->routeIs('laporan.*'),
            ],
            [
                'label' => 'Tetapan',
                'icon' => 'bi-gear-fill',
                'url' => route('tetapan.rujukan'),
                'roles' => ['SYSTEM'],
                'active' => request()->routeIs('tetapan.*'),
            ],
        ];
    }
}
