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

    
    {{-- Pengurusan tamat sesi --}}
    <form method="POST" action="{{ route('logout') }}" id="borangSesiTamat" class="d-none">
        @csrf
        <input type="hidden" name="sebab" value="tamat">
    </form>

    <script>
        (() => {
            const TEMPOH_MS = @json((int) config('session.lifetime')) * 60 * 1000;
            const AMARAN_MS = Math.min(2 * 60 * 1000, TEMPOH_MS / 2);
            const SELANG_PING_MS = Math.min(5 * 60 * 1000, TEMPOH_MS / 3);
            const URL_KEKAL = @json(route('sesi.kekal'));
            const URL_LOGIN = @json(route('login'));
            const KUNCI_SERVER = 'mpas_sesi_server_terakhir';
            const KUNCI_KELUAR = 'mpas_sesi_log_keluar';
            const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            const baca = (kunci, lalai) => {
                try {
                    return Number(localStorage.getItem(kunci)) || lalai;
                } catch (e) {
                    return lalai;
                }
            };

            const tulis = (kunci, nilai) => {
                try {
                    localStorage.setItem(kunci, String(nilai));
                } catch (e) {}
            };

            // Halaman ini baru dimuat = permintaan terkini ke server
            let serverTerakhir = Date.now();
            let aktivitiTerakhir = Date.now();
            let amaranDibuka = false;
            let sedangLogKeluar = false;

            tulis(KUNCI_SERVER, serverTerakhir);

            const formatMasa = (ms) => {
                const saat = Math.max(0, Math.ceil(ms / 1000));
                return `${Math.floor(saat / 60)}:${String(saat % 60).padStart(2, '0')}`;
            };

            const logKeluar = () => {
                if (sedangLogKeluar) {
                    return;
                }

                sedangLogKeluar = true;
                tulis(KUNCI_KELUAR, Date.now());
                document.getElementById('borangSesiTamat').submit();
            };

            const kekalkanSesi = async () => {
                try {
                    const respons = await fetch(URL_KEKAL, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                        },
                    });

                    if (respons.status === 401 || respons.status === 419) {
                        window.location.href = URL_LOGIN;
                        return;
                    }

                    if (respons.ok) {
                        serverTerakhir = Date.now();
                        tulis(KUNCI_SERVER, serverTerakhir);
                    }
                } catch (e) {
                    // Tiada sambungan: cuba semula pada semakan seterusnya
                }
            };

            const bukaAmaran = () => {
                amaranDibuka = true;

                const tamatPada = serverTerakhir + TEMPOH_MS;
                let selang;

                Swal.fire({
                    icon: 'warning',
                    title: 'Sesi hampir tamat',
                    html: `Anda tidak aktif untuk satu tempoh. Sesi akan tamat dalam <strong id="kiraanSesi">${formatMasa(tamatPada - Date.now())}</strong>.`,
                    showCancelButton: true,
                    confirmButtonText: 'Teruskan',
                    cancelButtonText: 'Log Keluar',
                    confirmButtonColor: '#168A7E',
                    cancelButtonColor: '#6B7C7A',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    timer: Math.max(0, tamatPada - Date.now()),
                    timerProgressBar: true,
                    didOpen: () => {
                        selang = setInterval(() => {
                            const kiraan = document.getElementById('kiraanSesi');

                            if (kiraan) {
                                kiraan.textContent = formatMasa(tamatPada - Date.now());
                            }
                        }, 500);
                    },
                    willClose: () => clearInterval(selang),
                }).then((hasil) => {
                    amaranDibuka = false;

                    if (hasil.isConfirmed) {
                        aktivitiTerakhir = Date.now();
                        kekalkanSesi();
                    } else if (hasil.dismiss === Swal.DismissReason.cancel || hasil.dismiss === Swal.DismissReason.timer) {
                        logKeluar();
                    }
                });
            };

            // Rekod aktiviti pengguna (diabaikan semasa amaran dibuka)
            ['mousedown', 'keydown', 'scroll', 'touchstart', 'mousemove'].forEach((jenis) => {
                window.addEventListener(jenis, () => {
                    if (! amaranDibuka) {
                        aktivitiTerakhir = Date.now();
                    }
                }, { passive: true });
            });

            // Semakan berkala
            setInterval(() => {
                if (sedangLogKeluar) {
                    return;
                }

                const sekarang = Date.now();

                // Ambil masa permintaan terkini daripada tab lain
                serverTerakhir = Math.max(serverTerakhir, baca(KUNCI_SERVER, serverTerakhir));

                // Pengguna aktif: panjangkan sesi secara senyap
                if (! amaranDibuka && sekarang - aktivitiTerakhir < SELANG_PING_MS && sekarang - serverTerakhir > SELANG_PING_MS) {
                    kekalkanSesi();
                    return;
                }

                const baki = serverTerakhir + TEMPOH_MS - sekarang;

                if (baki <= 0) {
                    Swal.close();
                    logKeluar();
                } else if (baki <= AMARAN_MS && ! amaranDibuka) {
                    bukaAmaran();
                }
            }, 15000);

            // Selaraskan dengan tab lain
            window.addEventListener('storage', (event) => {
                if (event.key === KUNCI_KELUAR) {
                    window.location.href = URL_LOGIN;
                }

                if (event.key === KUNCI_SERVER && amaranDibuka) {
                    const baru = Number(event.newValue) || 0;

                    if (baru + TEMPOH_MS - Date.now() > AMARAN_MS) {
                        serverTerakhir = baru;
                        Swal.close();
                    }
                }
            });
        })();
    </script>
    
    @stack('skrip')
</body>
</html>