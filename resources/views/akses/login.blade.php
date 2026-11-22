@extends('layouts.akses')

@section('tajuk', 'Log Masuk')

@section('kandungan')
    <main class="halaman-login min-vh-100 d-flex align-items-center justify-content-center p-3 position-relative overflow-hidden">
        {{-- ===================== DESKTOP: panel identiti sistem ===================== --}}
        <aside class="panel-jenama-login d-none d-lg-flex" aria-label="Maklumat sistem">
            <span aria-hidden="true"></span>

            <div class="jenama-login-isi">
                <p class="jenama-login-label">Majlis Daerah Marang</p>
                <h2 class="jenama-login-tajuk">Marang Property<br>Asset System</h2>
                <p class="jenama-login-penerangan">Sistem pengurusan aset tak alih Majlis Daerah Marang: satu platform untuk merekod, memantau dan melapor aset bangunan dan tanah.</p>

                <ul class="jenama-login-ciri">
                    <li><span><i class="bi bi-buildings"></i></span><div><strong>Aset Bangunan &amp; Tanah</strong><small>Rekod lengkap, dokumen dan lokasi peta</small></div></li>
                    <li><span><i class="bi bi-tools"></i></span><div><strong>Aduan &amp; Penyelenggaraan</strong><small>Aduan awam melalui kod QR hingga selesai</small></div></li>
                    <li><span><i class="bi bi-bar-chart-line"></i></span><div><strong>Laporan &amp; Analisis</strong><small>Laporan sedia cetak dan eksport Excel</small></div></li>
                </ul>
            </div>

            <div class="jenama-login-kaki">
                <span>Hakmilik Majlis Daerah Marang &copy; 2025</span>
                <span>MPAS v{{ config('app.version', '1.0') }}</span>
            </div>
        </aside>

        <div class="w-100 bekas-login">
            <div class="login-hero-apl d-md-none">
                <div class="login-hero-logo">
                    <img src="{{ asset('images/logo-mdm.png') }}" alt="Logo Majlis Daerah Marang">
                    <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo Marang Property Asset System">
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden kad-login">
                <div class="card-body p-4 p-md-5">
                    {{-- Desktop besar: tajuk borang (logo sudah di panel kiri) --}}
                    <div class="kepala-borang-login d-none d-lg-block">
                        <div class="logo-borang-login">
                            <img src="{{ asset('images/logo-mdm.png') }}" alt="Logo Majlis Daerah Marang">
                            <span class="pemisah-logo" aria-hidden="true"></span>
                            <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo Marang Property Asset System">
                        </div>

                        <h1>Log masuk ke akaun anda</h1>
                        <p class="text-secondary mb-0">Masukkan nama pengguna dan kata laluan MPAS anda.</p>
                    </div>

                    <div class="text-center mb-4 d-none d-md-block d-lg-none">
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                            <img src="{{ asset('images/logo-mdm.png') }}" alt="Logo Majlis Daerah Marang" class="logo-login">
                            <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo Marang Property Asset System" class="logo-login">
                        </div>
                    </div>

                    <div class="login-sapaan d-md-none">
                        <h1>Selamat datang</h1>
                        <p>Log masuk untuk teruskan</p>
                    </div>

                    <form method="POST" action="{{ route('authenticate') }}" data-borang-log-masuk>
                        @csrf

                        @if ($errors->any())
                            <div class="amaran-login d-none d-lg-flex" role="alert">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="usr_name" class="form-label fw-semibold">Nama Pengguna</label>

                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control @error('usr_name') is-invalid @enderror" id="usr_name" name="usr_name" value="{{ old('usr_name') }}" autocomplete="username" autocapitalize="none" autofocus required>
                            </div>

                            @error('usr_name')
                                <div class="text-danger small mt-2 d-lg-none">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Kata Laluan</label>

                            <div class="input-group input-kata-laluan">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="current-password" required>
                                <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#password" aria-label="Papar kata laluan" title="Papar kata laluan"><i class="bi bi-eye"></i></button>
                            </div>

                            <div class="amaran-capslock" data-amaran-capslock hidden><i class="bi bi-capslock-fill"></i> Caps Lock dihidupkan</div>

                            @error('password')
                                <div class="text-danger small mt-2 d-lg-none">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold butang-log-masuk" data-butang-log-masuk><i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk</button>
                    </form>

                    {{-- Desktop besar: pautan awam & nota keselamatan --}}
                    <div class="pautan-awam-login d-none d-lg-flex">
                        <a href="{{ route('portal.sewa') }}"><i class="bi bi-shop"></i> Portal Awam</a>
                        <span aria-hidden="true">·</span>
                        <a href="{{ route('aduan.status') }}"><i class="bi bi-search"></i> Semak Status Aduan</a>
                    </div>

                    <p class="nota-keselamatan-login d-none d-lg-flex"><i class="bi bi-shield-lock"></i> Akses terhad kepada kakitangan Majlis Daerah Marang yang diberi kebenaran.</p>

                    {{-- Telefon & tablet: pautan awam gaya app --}}
                    <p class="pautan-awam-apl d-lg-none">
                        <a href="{{ route('portal.sewa') }}"><i class="bi bi-shop"></i> Portal Awam</a>
                        <span aria-hidden="true">·</span>
                        <a href="{{ route('aduan.status') }}"><i class="bi bi-search"></i> Semak Status Aduan</a>
                    </p>

                    <div class="border-top mt-4 pt-3 text-center kaki-login d-lg-none">
                        <p class="text-secondary small mb-0">Hakmilik Majlis Daerah Marang © 2025</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Elak "419 Page Expired": ambil token CSRF terkini sebelum log masuk.
        // (Token lama boleh tamat jika halaman dibiarkan lama, atau sesi ditukar oleh tab lain.)
        (() => {
            const borang = document.querySelector('[data-borang-log-masuk]');

            if (! borang) {
                return;
            }

            // Halaman dipulihkan daripada cache pelayar (butang Back): muat semula
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    window.location.reload();
                }
            });

            let sedia = false;
            const butang = borang.querySelector('[data-butang-log-masuk]');

            // Amaran Caps Lock pada medan kata laluan
            const kataLaluan = borang.querySelector('#password');
            const amaranCaps = borang.querySelector('[data-amaran-capslock]');
            const semakCaps = (event) => {
                if (amaranCaps && event.getModifierState) {
                    amaranCaps.hidden = ! event.getModifierState('CapsLock');
                }
            };
            kataLaluan?.addEventListener('keydown', semakCaps);
            kataLaluan?.addEventListener('keyup', semakCaps);
            kataLaluan?.addEventListener('blur', () => { if (amaranCaps) amaranCaps.hidden = true; });

            borang.addEventListener('submit', async (event) => {
                if (sedia) {
                    // Keadaan memproses: elak tekan dua kali
                    if (butang) {
                        butang.disabled = true;
                        butang.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Sedang log masuk…';
                    }
                    return;
                }

                event.preventDefault();

                try {
                    const respons = await fetch(@json(route('login')), {
                        credentials: 'same-origin',
                        headers: { Accept: 'text/html' },
                        cache: 'no-store',
                    });

                    // Sudah log masuk (contoh: di tab lain) → terus ke halaman dituju
                    if (respons.redirected && ! respons.url.includes('/login')) {
                        window.location.href = respons.url;
                        return;
                    }

                    const html = await respons.text();
                    const token = new DOMParser()
                        .parseFromString(html, 'text/html')
                        .querySelector('input[name="_token"]')?.value;

                    if (token) {
                        borang.querySelector('input[name="_token"]').value = token;
                    }
                } catch (ralat) {
                    // Tiada sambungan: hantar dengan token sedia ada
                }

                sedia = true;
                borang.requestSubmit ? borang.requestSubmit() : borang.submit();
            });
        })();
    </script>
@endsection
