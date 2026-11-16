@extends('layouts.akses')

@section('tajuk', 'Log Masuk')

@section('kandungan')
    <main class="halaman-login min-vh-100 d-flex align-items-center justify-content-center p-3 position-relative overflow-hidden">
        <div class="w-100 bekas-login">
            <div class="login-hero-apl d-md-none">
                <div class="login-hero-logo">
                    <img src="{{ asset('images/logo-mdm.png') }}" alt="Logo Majlis Daerah Marang">
                    <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo Marang Property Asset System">
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden kad-login">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4 d-none d-md-block">
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

                        <div class="mb-3">
                            <label for="usr_name" class="form-label fw-semibold">Nama Pengguna</label>

                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control @error('usr_name') is-invalid @enderror" id="usr_name" name="usr_name" value="{{ old('usr_name') }}" autocomplete="username" autocapitalize="none" autofocus required>
                            </div>

                            @error('usr_name')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Kata Laluan</label>

                            <div class="input-group input-kata-laluan">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="current-password" required>
                                <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#password" aria-label="Papar kata laluan" title="Papar kata laluan"><i class="bi bi-eye"></i></button>
                            </div>

                            @error('password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold butang-log-masuk"><i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk</button>
                    </form>
                    <div class="border-top mt-4 pt-3 text-center kaki-login">
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

            borang.addEventListener('submit', async (event) => {
                if (sedia) {
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
