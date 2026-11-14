# MPAS: No. 9, pindahkan logik dari view ke controller

Disediakan pada 11 November 2026. **Status: belum dibuat.**

Refactor dalaman sahaja. Rupa dan fungsi sistem tidak berubah. Nombor baris berdasarkan fail pada 11/11/2026, jadi semak semula kalau fail sudah diubah sejak itu.

| Bahagian | Perkara | Fail |
|---|---|---|
| A | Data borang bangunan | `PengurusanBangunanController.php`, `bangunan/borang.blade.php` |
| B | Pemeriksaan dalam butiran | `PengurusanBangunanController.php`, `bangunan/butiran.blade.php` |
| C | Statistik Tetapan Komponen | `TetapanKomponenController.php`, `tetapan/komponen.blade.php` |
| D | Menu sidebar dan nama peranan | `app/Support/Peranan.php` (baru), `app/Support/MenuSidebar.php` (baru), `AppServiceProvider.php`, `TetapanController.php`, `layouts/utama.blade.php`, `profil/kemaskini.blade.php`, `pengguna/kemaskini.blade.php` |

Setiap bahagian boleh dibuat dan diuji secara berasingan.

---

## Bahagian A: Data borang bangunan

### A1. `app/Http/Controllers/PengurusanBangunanController.php`

**(a)** Gantikan fungsi `create()`:

```php
    public function create(): View
    {
        return view('bangunan.daftar');
    }
```

dengan:

```php
    public function create(Pemeriksaan $pemeriksaan): View
    {
        return view('bangunan.daftar', $this->dataBorang($pemeriksaan));
    }
```

**(b)** Gantikan fungsi `edit()`:

```php
    public function edit(Bangunan $bangunan): View
    {
        $bangunan->load([
            'penyewa',
            'dokumen.dimuatNaikOleh',
            'penyelenggaraanTerkini.pemeriksa',
        ]);

        return view('bangunan.kemaskini', compact('bangunan'));
    }
```

dengan:

```php
    public function edit(Bangunan $bangunan, Pemeriksaan $pemeriksaan): View
    {
        $bangunan->load([
            'penyewa',
            'dokumen.dimuatNaikOleh',
            'penyelenggaraanTerkini.pemeriksa',
        ]);

        return view('bangunan.kemaskini', array_merge(
            compact('bangunan'),
            $this->dataBorang($pemeriksaan, $bangunan)
        ));
    }

    /**
     * Data yang diperlukan oleh borang daftar dan kemas kini bangunan.
     */
    private function dataBorang(Pemeriksaan $pemeriksaan, ?Bangunan $bangunan = null): array
    {
        $komponenPemeriksaan = $pemeriksaan->komponenAktif();

        return [
            'penyewaAktif' => $bangunan?->penyewa->where('tnt_status', 'AKTIF')->values() ?? collect(),
            'komponenPemeriksaan' => $komponenPemeriksaan,
            'jumlahItemPemeriksaan' => $komponenPemeriksaan->sum(fn ($komponen) => count($komponen->itemAktif())),
            'bolehIsiPemeriksaan' => TetapanPenyelenggaraan::bolehIsi(auth()->user()),
            'pemeriksaanTerkini' => $bangunan?->penyelenggaraanTerkini,
        ];
    }
```

`use App\Services\Pemeriksaan;` dan `use App\Models\TetapanPenyelenggaraan;` sudah ada di atas fail.

### A2. `resources/views/bangunan/borang.blade.php`

**(a)** Di atas fail (baris 1–9), gantikan:

```blade
@php
    $bangunan = $bangunan ?? null;
    $penyewaAktif = $bangunan?->penyewa?->where('tnt_status', 'AKTIF')->values() ?? collect();

    $komponenPemeriksaan = app(\App\Services\Pemeriksaan::class)->komponenAktif();
    $bolehIsiPemeriksaan = \App\Models\TetapanPenyelenggaraan::bolehIsi(auth()->user());
    $pemeriksaanTerkini = $bangunan?->penyelenggaraanTerkini;
    $modPemeriksaan = old('pemeriksaan.mod', $bangunan ? 'TIADA' : 'BARU');
@endphp
```

dengan:

```blade
@php
    $bangunan = $bangunan ?? null;
    $modPemeriksaan = old('pemeriksaan.mod', $bangunan ? 'TIADA' : 'BARU');
@endphp
```

**(b)** Sekitar baris 517–519, di hujung blok `@php` yang mengandungi `$pilihanMod` dan `$nilaiPemeriksaan`, padam baris ini sahaja (kekalkan `@endphp`):

```blade

                                $jumlahItemPemeriksaan = $komponenPemeriksaan->sum(
                                    fn ($komponen) => count($komponen->itemAktif())
                                );
```

`$modPemeriksaan`, `$pilihanMod` dan `$nilaiPemeriksaan` kekal dalam view kerana ia konfigurasi paparan. `daftar.blade.php` dan `kemaskini.blade.php` tidak perlu diubah, kerana `@include` berkongsi pemboleh ubah daripada controller.

---

## Bahagian B: Pemeriksaan dalam butiran

### B1. `PengurusanBangunanController.php`, fungsi `butiran()`

Di hujung fungsi, gantikan:

```php
        return view('bangunan.butiran', compact('bangunan', 'bolehLihatPemeriksaan'));
```

dengan:

```php
        $senaraiPemeriksaan = $bolehLihatPemeriksaan ? $bangunan->penyelenggaraan : collect();

        return view('bangunan.butiran', [
            'bangunan' => $bangunan,
            'bolehLihatPemeriksaan' => $bolehLihatPemeriksaan,
            'pemeriksaanTerkini' => $senaraiPemeriksaan->first(),
            'sejarahPemeriksaan' => $senaraiPemeriksaan->slice(1)->values(),
        ]);
```

### B2. `resources/views/bangunan/butiran.blade.php`

Sekitar baris 334–338, padam:

```blade
                @php
                    $senaraiPemeriksaan = $bangunan->penyelenggaraan;
                    $pemeriksaanTerkini = $senaraiPemeriksaan->first();
                    $sejarahPemeriksaan = $senaraiPemeriksaan->slice(1);
                @endphp

```

---

## Bahagian C: Statistik Tetapan Komponen

### C1. `app/Http/Controllers/TetapanKomponenController.php`

Gantikan fungsi `index()` dengan:

```php
    public function index(): View
    {
        $komponen = KomponenPenyelenggaraan::query()
            ->orderBy('amc_order')
            ->orderBy('amc_id')
            ->get();

        $jumlahItem = $komponen->sum(fn ($item) => count($item->amc_items ?? []));
        $jumlahItemAktif = $komponen->sum(fn ($item) => count($item->itemAktif()));

        return view('tetapan.komponen', [
            'komponen' => $komponen,
            'jumlahKomponenAktif' => $komponen->where('amc_status', 'ACTIVE')->count(),
            'jumlahItemAktif' => $jumlahItemAktif,
            'jumlahItemTidakAktif' => $jumlahItem - $jumlahItemAktif,
            'turutanSeterusnya' => ($komponen->max('amc_order') ?? 0) + 1,
        ]);
    }
```

### C2. `resources/views/tetapan/komponen.blade.php`

**(a)** Di atas fail (baris 6–11), padam:

```blade
    @php
        $jumlahKomponenAktif = $komponen->where('amc_status', 'ACTIVE')->count();
        $jumlahItem = $komponen->sum(fn ($item) => count($item->amc_items ?? []));
        $jumlahItemAktif = $komponen->sum(fn ($item) => count($item->itemAktif()));
        $turutanSeterusnya = ($komponen->max('amc_order') ?? 0) + 1;
    @endphp

```

**(b)** Dalam kad "Item Tidak Aktif", tukar:

```blade
                            <strong>{{ $jumlahItem - $jumlahItemAktif }}</strong>
```

kepada:

```blade
                            <strong>{{ $jumlahItemTidakAktif }}</strong>
```

---

## Bahagian D: Menu sidebar dan nama peranan

Senarai nama peranan sekarang ditakrif di 4 tempat. Bahagian ini menjadikannya satu sumber sahaja.

### D1. Cipta `app/Support/Peranan.php`

```bash
php artisan make:class Support/Peranan
```

Padam semua kandungan fail dan tampal:

```php
<?php

namespace App\Support;

class Peranan
{
    /**
     * Nama penuh bagi setiap peranan pengguna MPAS.
     */
    public const NAMA = [
        'SYSTEM' => 'Super Admin',
        'BPH' => 'Bahagian Pengurusan Harta',
        'BP' => 'Bahagian Perancang',
        'KJ' => 'Jabatan Kejuruteraan',
    ];

    public static function nama(?string $kod): string
    {
        return self::NAMA[$kod] ?? (string) $kod;
    }
}
```

### D2. Cipta `app/Support/MenuSidebar.php`

```bash
php artisan make:class Support/MenuSidebar
```

Padam semua kandungan fail dan tampal:

```php
<?php

namespace App\Support;

class MenuSidebar
{
    /**
     * Senarai menu sidebar (desktop dan telefon).
     * Untuk tambah menu baru, tambah satu entri di sini sahaja.
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
```

Kalau menu dalam layout sudah berubah sejak 11/11/2026, salin versi terkini array `$menu` dari layout ke sini.

### D3. `app/Providers/AppServiceProvider.php`

Padam semua kandungan fail dan tampal:

```php
<?php

namespace App\Providers;

use App\Support\MenuSidebar;
use App\Support\Peranan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Paginator::useBootstrapFive();

        // Nama peranan tersedia dalam semua view sebagai $namaPeranan
        View::share('namaPeranan', Peranan::NAMA);

        // Data layout utama: pengguna semasa dan menu sidebar
        View::composer('layouts.utama', function ($view): void {
            $view->with([
                'user' => auth()->user(),
                'menu' => MenuSidebar::senarai(),
            ]);
        });
    }
}
```

Kalau `AppServiceProvider.php` sudah ada tambahan lain sejak 11/11/2026, kekalkan tambahan itu dan hanya tambah baris `use` serta dua blok `View::` di atas.

### D4. `resources/views/layouts/utama.blade.php`

Selepas `<body class="bg-light">`, padam keseluruhan blok `@php ... @endphp` (sekitar baris 48–117), dari `$user = auth()->user();` hingga `@endphp` selepas array `$menu`. Gelung `@foreach ($menu as $item)` dan `in_array($user->usr_level, $item['roles'])` tidak perlu diubah.

### D5. `resources/views/profil/kemaskini.blade.php`

Gantikan blok `@php` di atas fail dengan:

```blade
    @php
        $inisial = strtoupper(substr($pengguna->usr_name, 0, 1));
    @endphp
```

### D6. `resources/views/pengguna/kemaskini.blade.php`

Gantikan blok `@php` di atas fail dengan:

```blade
    @php
        $inisial = strtoupper(substr($pengguna->usr_name, 0, 1));
        $aktif = $pengguna->usr_status === 'ACTIVE';
    @endphp
```

### D7. `app/Http/Controllers/TetapanController.php`, fungsi `penyelenggaraan()`

Gantikan:

```php
        $namaPeranan = [
            'BPH' => 'Bahagian Pengurusan Harta',
            'BP' => 'Bahagian Perancang',
            'KJ' => 'Jabatan Kejuruteraan',
        ];

        return view('tetapan.penyelenggaraan', compact('tetapan', 'peranan', 'namaPeranan'));
```

dengan:

```php
        return view('tetapan.penyelenggaraan', compact('tetapan', 'peranan'));
```

---

## Kosongkan cache

```bash
composer dump-autoload
php artisan view:clear
php artisan config:clear
```

## Senarai ujian

| # | Uji | Jangkaan |
|---|---|---|
| 1 | Sidebar (SYSTEM, BPH, BP, KJ) | Menu sama seperti sebelum ini bagi setiap peranan. Menu aktif ditanda |
| 2 | Menu telefon | Senarai menu sama |
| 3 | Avatar pengguna di bar atas | Nama dan nama penuh peranan |
| 4 | Profil Saya, dan Pengguna → Kemas kini | Nama penuh peranan betul |
| 5 | Tetapan → Akses Penyelenggaraan | Kad BPH, BP dan KJ dengan nama penuh |
| 6 | Tetapan → Komponen Penyelenggaraan | 3 kad statistik dengan nombor yang sama |
| 7 | Daftar bangunan → langkah 5 | Borang pemeriksaan dengan kemajuan "0 / 13 item" |
| 8 | Kemas kini bangunan yang ada penyewa dan pemeriksaan | Penyewa aktif di langkah 7. Pilihan dan maklumat pemeriksaan terkini berfungsi |
| 9 | Butiran bangunan → tab 5 | Pemeriksaan terkini dan sejarah dipaparkan |

Kalau ada ralat seperti "Undefined variable $menu" atau "Class App\Support\MenuSidebar not found", semak bahawa `composer dump-autoload` sudah dijalankan dan namespace tepat.
