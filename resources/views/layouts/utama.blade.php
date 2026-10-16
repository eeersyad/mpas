<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('tajuk', 'Dashboard') | MPAS</title>

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
                'roles' => ['SYSTEM', 'BPH'],
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
                'label' => 'Tetapan',
                'icon' => 'bi-gear-fill',
                'url' => route('tetapan.rujukan'),
                'roles' => ['SYSTEM'],
                'active' => request()->routeIs('tetapan.*'),
            ],
        ];
    @endphp

    <div class="d-flex min-vh-100" id="aplikasi">
        <aside class="sidebar d-none d-lg-flex flex-column">
            <a href="{{ route('dashboard') }}" class="jenama text-decoration-none">
                <span class="jenama-ikon">
                    <i class="bi bi-buildings-fill"></i>
                </span>

                <span>
                    <strong>MPAS</strong>
                    <small>Sistem Aset Tak Alih</small>
                </span>
            </a>

            <p class="menu-label">MENU UTAMA</p>

            <nav class="nav flex-column menu-sisi">
                @foreach ($menu as $item)
                    @if (in_array($user->usr_level, $item['roles']))
                        <a href="{{ $item['url'] }}" class="menu-pautan {{ $item['active'] ? 'aktif' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mt-auto">
                <p class="menu-label">AKAUN</p>

                <nav class="nav flex-column menu-sisi">
                    <a href="{{ route('profil.edit') }}" class="menu-pautan">
                        <i class="bi bi-person-circle"></i>
                        <span>Profil Saya</span>
                    </a>
                </nav>
            </div>
        </aside>

        <div class="kandungan-sistem flex-grow-1">
            <header class="bar-atas d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="butang-toggle d-none d-lg-grid"
                        type="button"
                        id="toggleSidebar"
                        aria-label="Kecilkan sidebar"
                        aria-expanded="true"
                    >
                        <i class="bi bi-layout-sidebar-inset"></i>
                    </button>

                    <button
                        class="btn butang-menu d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#menuTelefon"
                        aria-label="Buka menu"
                    >
                        <i class="bi bi-list"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button class="butang-ikon" type="button" data-notifikasi aria-label="Notifikasi">
                        <i class="bi bi-bell"></i>
                        <span class="bulatan-notifikasi"></span>
                    </button>

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

    <div class="offcanvas offcanvas-start menu-telefon" tabindex="-1" id="menuTelefon">
        <div class="offcanvas-header">
            <span class="jenama">
                <span class="jenama-ikon">
                    <i class="bi bi-buildings-fill"></i>
                </span>

                <span>
                    <strong>MPAS</strong>
                    <small>Sistem Aset Tak Alih</small>
                </span>
            </span>

            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>

        <div class="offcanvas-body">
            <nav class="nav flex-column menu-sisi">
                @foreach ($menu as $item)
                    @if (in_array($user->usr_level, $item['roles']))
                        <a
                            href="{{ $item['url'] }}"
                            class="menu-pautan {{ $item['active'] ? 'aktif' : '' }}"
                            data-bs-dismiss="offcanvas"
                        >
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @if (session('berjaya'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: @json(session('berjaya')),
                confirmButtonText: 'Faham',
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
                confirmButtonText: 'Faham',
                confirmButtonColor: '#168A7E',
            });
        </script>
    @endif

    <script>
        document.querySelector('[data-notifikasi]')?.addEventListener('click', () => {
            Swal.fire({
                icon: 'info',
                title: 'Tiada notifikasi baharu',
                text: 'Notifikasi sistem akan dipaparkan di sini.',
                confirmButtonText: 'Faham',
                confirmButtonColor: '#168A7E',
            });
        });
    </script>
</body>
</html>