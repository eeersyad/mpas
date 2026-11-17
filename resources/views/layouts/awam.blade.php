<!DOCTYPE html>
<html lang="ms">
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

<body class="halaman-awam @yield('kelas_body')">
    <main class="container py-4 py-md-5">
        @yield('kandungan')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.all.min.js" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

    @if (session('berjaya'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Aduan berjaya dihantar',
                text: @json(session('berjaya')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#168A7E',
            });
        </script>
    @endif

    @stack('skrip')
</body>
</html>