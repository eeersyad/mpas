<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('tajuk', 'Dashboard') | MPAS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    @stack('gaya')

    <script>
        (() => {
            const akar = document.documentElement;
            akar.classList.add('sidebar-memuat');

            try {
                const status = localStorage.getItem('mpas_sidebar');

                if (status === 'mini' || status === 'runtuh') {
                    akar.classList.add('sidebar-mini');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    @php
        $user = auth()->user();
        
        $namaPeranan = [
            'SYSTEM' => 'Super Admin',
            'BPH' => 'Bahagian Pengurusan Harta',
            'BP' => 'Bahagian Perancang',
            'KJ' => 'Jabatan Kejuruteraan',
        ];

        $menu = [
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
    @endphp

    <div
        class="d-flex min-vh-100"
        id="aplikasi"
        data-user-id="{{ $user->usr_id }}"
    >
        <aside class="sidebar d-none d-lg-flex flex-column" aria-label="Menu utama">
            <a href="{{ route('dashboard') }}" class="jenama jenama-logo text-decoration-none" aria-label="Dashboard MPAS" data-tooltip-sidebar="Dashboard MPAS">
                <img src="{{ asset('images/logo-mpas.png') }}" alt="Logo MPAS" class="logo-sidebar logo-penuh">
                <img src="{{ asset('images/logo-mpas-mini.png') }}" alt="" class="logo-mini" aria-hidden="true">
            </a>

            <p class="menu-label"><span>MENU UTAMA</span></p>

            <nav class="nav flex-column menu-sisi">
                @foreach ($menu as $item)
                    @if (in_array($user->usr_level, $item['roles']))
                        <a href="{{ $item['url'] }}" class="menu-pautan {{ $item['active'] ? 'aktif' : '' }}" aria-label="{{ $item['label'] }}" data-tooltip-sidebar="{{ $item['label'] }}" @if ($item['active']) aria-current="page" @endif>
                            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mt-auto">
                <p class="menu-label"><span>AKAUN</span></p>

                <nav class="nav flex-column menu-sisi">
                    <a href="{{ route('profil.edit') }}" class="menu-pautan {{ request()->routeIs('profil.*') ? 'aktif' : '' }}" aria-label="Profil Saya" data-tooltip-sidebar="Profil Saya">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <span>Profil Saya</span>
                    </a>
                </nav>
            </div>
        </aside>

        <div class="kandungan-sistem flex-grow-1">
            <header class="bar-atas d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <button class="butang-toggle d-none d-lg-grid" type="button" id="toggleSidebar" aria-label="Kecilkan sidebar" aria-expanded="true" title="Kecilkan sidebar">
                        <i class="bi bi-layout-sidebar-inset" aria-hidden="true"></i>
                    </button>

                    <button
                        class="btn butang-menu d-lg-none"
                        type="button"
                        id="toggleMenuTelefon"
                        aria-label="Buka menu"
                        aria-controls="menuTelefon"
                        aria-expanded="false"
                    >
                        <i class="bi bi-list"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button
                            class="butang-ikon"
                            type="button"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside"
                            aria-expanded="false"
                            aria-label="Buka notifikasi"
                        >
                            <i class="bi bi-bell"></i>

                            <span
                                class="bulatan-notifikasi"
                                data-lencana-notifikasi
                                hidden
                            ></span>
                        </button>

                        <div
                            class="dropdown-menu dropdown-menu-end panel-notifikasi"
                            data-panel-notifikasi
                            data-url-senarai="{{ route('notifikasi.index') }}"
                            data-url-baca="{{ url('/notifikasi') }}"
                            data-url-baca-semua="{{ route('notifikasi.baca-semua') }}"
                        >
                            <div class="kepala-notifikasi">
                                <div>
                                    <strong>Notifikasi</strong>
                                </div>

                                <button
                                    type="button"
                                    class="butang-baca-semua"
                                    data-baca-semua
                                >
                                    Tandakan semua dibaca
                                </button>
                            </div>

                            <div class="senarai-notifikasi" data-senarai-notifikasi>
                                <div class="notifikasi-kosong">
                                    <i class="bi bi-bell-slash"></i>
                                    <p>Memuatkan notifikasi...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button
                            class="btn pengguna-menu d-flex align-items-center gap-2"
                            type="button"
                            data-bs-toggle="dropdown"
                        >
                            <span class="avatar-pengguna">
                                {{ strtoupper(substr($user->usr_name, 0, 1)) }}
                            </span>

                            <span class="d-none d-sm-block text-start">
                                <strong>{{ $user->usr_name }}</strong>
                                <small>{{ $namaPeranan[$user->usr_level] }}</small>
                            </span>

                            <i class="bi bi-chevron-down kecil"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end bayang-menu menu-pengguna">
                            <li>
                                <a class="dropdown-item" href="{{ route('profil.edit') }}">
                                    <i class="bi bi-person-circle me-2"></i>Profil Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Log Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="p-3 p-md-4 p-xl-5">
                @yield('kandungan')
            </main>
        </div>
    </div>

    <aside
        class="menu-telefon"
        id="menuTelefon"
        aria-labelledby="tajukMenuTelefon"
        aria-hidden="true"
    >
        <div class="offcanvas-header">
            <h2 id="tajukMenuTelefon" class="visually-hidden">Menu navigasi</h2>
            <span class="jenama jenama-logo">
                <img
                    src="{{ asset('images/logo-mpas-2.png') }}"
                    alt="Logo MPAS"
                    class="logo-sidebar"
                >
            </span>

            <button
                type="button"
                class="btn-close"
                data-tutup-menu-telefon
                aria-label="Tutup"
            ></button>
        </div>

        <div class="offcanvas-body">
            <nav class="nav flex-column menu-sisi">
                @foreach ($menu as $item)
                    @if (in_array($user->usr_level, $item['roles']))
                        <a
                            href="{{ $item['url'] }}"
                            class="menu-pautan {{ $item['active'] ? 'aktif' : '' }}"
                            data-pautan-menu-telefon
                        >
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </aside>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @if (session('berjaya'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: @json(session('berjaya')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#168A7E',
            });
        </script>
    @endif

    @if (session('ralat'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Tidak berjaya',
                text: @json(session('ralat')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#168A7E',
            });
        </script>
    @endif

    
    @stack('skrip')
</body>
</html>