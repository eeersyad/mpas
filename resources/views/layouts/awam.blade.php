<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('tajuk', 'Aduan Aset') | MPAS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">

    @vite(['resources/css/app.css'])

    @stack('kepala')
</head>

<body class="halaman-awam @yield('kelas_body') @hasSection('dwibahasa') ada-togol-bahasa @endif">
    @hasSection('dwibahasa')
        @php($bahasaSemasa = app()->getLocale() === 'en' ? 'en' : 'ms')
        <nav class="togol-bahasa" aria-label="{{ __('Pilih bahasa') }}">
            <a href="{{ route('bahasa', 'ms') }}" class="{{ $bahasaSemasa === 'ms' ? 'aktif' : '' }}" hreflang="ms" title="Bahasa Melayu" aria-label="Bahasa Melayu" @if ($bahasaSemasa === 'ms') aria-current="true" @endif><svg viewBox="0 0 28 14" aria-hidden="true"><rect width="28" height="14" fill="#cc0001"/><path d="M0 1h28M0 3h28M0 5h28M0 7h28M0 9h28M0 11h28M0 13h28" stroke="#fff" stroke-width="1"/><rect width="14" height="8" fill="#010066"/><circle cx="5" cy="4" r="2.6" fill="#fc0"/><circle cx="5.8" cy="4" r="2.15" fill="#010066"/><path d="M9.3 1.7l.45 1.5 1.45-.6-.9 1.3 1.35.8-1.55.15.25 1.55-1.05-1.15-1.05 1.15.25-1.55-1.55-.15 1.35-.8-.9-1.3 1.45.6z" fill="#fc0"/></svg><span>BM</span></a>
            <a href="{{ route('bahasa', 'en') }}" class="{{ $bahasaSemasa === 'en' ? 'aktif' : '' }}" hreflang="en" title="English" aria-label="English" @if ($bahasaSemasa === 'en') aria-current="true" @endif><svg viewBox="0 0 60 30" aria-hidden="true"><rect width="60" height="30" fill="#012169"/><path d="M0 0l60 30M60 0L0 30" stroke="#fff" stroke-width="6"/><path d="M0 0l60 30M60 0L0 30" stroke="#c8102e" stroke-width="2"/><path d="M30 0v30M0 15h60" stroke="#fff" stroke-width="10"/><path d="M30 0v30M0 15h60" stroke="#c8102e" stroke-width="6"/></svg><span>EN</span></a>
        </nav>
    @endif

    <main class="container py-4 py-md-5">
        @yield('kandungan')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.all.min.js" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

    @if (session('berjaya'))
        <script>
            Swal.fire({
                icon: 'success',
                title: @json(__('Aduan berjaya dihantar')),
                text: @json(session('berjaya')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#168A7E',
            });
        </script>
    @endif

    @stack('skrip')
</body>
</html>