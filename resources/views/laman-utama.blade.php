<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="{{ __('Sistem Pengurusan Aset Tak Alih Majlis Daerah Marang.') }}">
    <meta name="theme-color" content="#0a5c53">

    <title>MPAS | Majlis Daerah Marang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">

    @vite(['resources/css/app.css'])
</head>

<body class="laman-utama">
    <header class="laman-utama-bar">
        <a href="{{ route('utama') }}" class="laman-utama-logo" aria-label="MPAS">
            <img src="{{ asset('images/logo-mdm.png') }}" alt="Logo Majlis Daerah Marang">
            <span class="pemisah-logo" aria-hidden="true"></span>
            <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo Marang Property Asset System">
        </a>

        <nav class="laman-utama-nav" aria-label="{{ __('Navigasi') }}">
            <div class="laman-utama-bahasa" role="group" aria-label="{{ __('Bahasa') }}">
                <a href="{{ route('bahasa', 'ms') }}" @class(['aktif' => app()->getLocale() === 'ms'])>BM</a>
                <a href="{{ route('bahasa', 'en') }}" @class(['aktif' => app()->getLocale() === 'en'])>EN</a>
            </div>

            <a href="{{ route('login') }}" class="laman-utama-log-masuk" title="{{ __('Log Masuk Kakitangan') }}">
                <i class="bi bi-person-lock" aria-hidden="true"></i>
                <span class="d-none d-md-inline">{{ __('Log Masuk Kakitangan') }}</span>
                <span class="d-md-none">{{ __('Kakitangan') }}</span>
            </a>
        </nav>
    </header>

    <div class="laman-utama-hero">
        <div class="laman-utama-tajuk">
            <span class="laman-utama-label">MARANG PROPERTY ASSET SYSTEM</span>
            <h1>{{ __('Selamat datang ke MPAS') }}</h1>
            <p>{{ __('Sistem pengurusan aset tak alih Majlis Daerah Marang. Pilih perkhidmatan di bawah untuk bermula.') }}</p>
        </div>
    </div>

    <main class="laman-utama-isi">
        <div class="laman-utama-kad">
            <a href="{{ route('portal.sewa') }}" class="laman-utama-kad-item">
                <span class="laman-utama-kad-ikon"><i class="bi bi-shop"></i></span>
                <span class="laman-utama-kad-teks">
                    <strong>{{ __('Portal Awam') }}</strong>
                    <small>{{ __('Lihat premis milik Majlis Daerah Marang yang tersedia untuk disewa.') }}</small>
                </span>
                <span class="laman-utama-kad-panah" aria-hidden="true"><i class="bi bi-arrow-right"></i></span>
            </a>

            <a href="{{ route('aduan.status') }}" class="laman-utama-kad-item">
                <span class="laman-utama-kad-ikon"><i class="bi bi-search"></i></span>
                <span class="laman-utama-kad-teks">
                    <strong>{{ __('Semak Status Aduan') }}</strong>
                    <small>{{ __('Semak perkembangan aduan anda menggunakan no. rujukan aduan.') }}</small>
                </span>
                <span class="laman-utama-kad-panah" aria-hidden="true"><i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

        {{-- Telefon: senarai gaya aplikasi --}}
        <div class="d-md-none">
            <h2 class="laman-utama-seksyen">{{ __('Hubungi kami') }}</h2>

            <div class="laman-utama-senarai">
                @foreach ($hubungan['telefon'] as $talian)
                    <a href="tel:{{ $talian['tel'] }}" class="laman-utama-baris">
                        <span class="laman-utama-baris-ikon"><i class="bi bi-telephone-fill"></i></span>
                        <span class="laman-utama-baris-teks"><small>{{ $talian['label'] }}</small><strong>{{ $talian['nombor'] }}</strong></span>
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    </a>
                @endforeach

                <a href="mailto:{{ $hubungan['emel'] }}" class="laman-utama-baris">
                    <span class="laman-utama-baris-ikon"><i class="bi bi-envelope-fill"></i></span>
                    <span class="laman-utama-baris-teks"><small>{{ __('Emel') }}</small><strong>{{ $hubungan['emel'] }}</strong></span>
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </main>

    <footer class="laman-utama-kaki">
        <div class="laman-utama-hubungi d-none d-md-flex">
            <span class="laman-utama-hubungi-tajuk"><i class="bi bi-headset" aria-hidden="true"></i> {{ __('Hubungi kami') }}</span>
            @foreach ($hubungan['telefon'] as $talian)
                <a href="tel:{{ $talian['tel'] }}"><i class="bi bi-telephone" aria-hidden="true"></i> {{ $talian['nombor'] }}</a>
            @endforeach
            <a href="mailto:{{ $hubungan['emel'] }}"><i class="bi bi-envelope" aria-hidden="true"></i> {{ $hubungan['emel'] }}</a>
        </div>

        <div class="laman-utama-hakcipta">{{ __('Hakmilik Majlis Daerah Marang') }} &copy; 2025</div>
    </footer>
</body>
</html>
