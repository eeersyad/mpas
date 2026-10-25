<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('tajuk', 'Log Masuk') | MPAS</title>
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light overflow-hidden">
    @yield('kandungan')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('berjaya'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berjaya',
                text: @json(session('berjaya')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#0f4c81',
            });
        </script>
    @endif
</body>
</html>