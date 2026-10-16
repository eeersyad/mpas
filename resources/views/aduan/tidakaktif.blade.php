@extends('layouts.awam')

@section('tajuk', 'Kod QR Tidak Aktif')

@section('kandungan')
    <div class="kad-awam text-center mx-auto" style="max-width: 560px;">
        <div class="awam-ikon mb-4">
            <i class="bi bi-qr-code"></i>
        </div>

        <h1 class="h4 fw-bold">Kod QR Tidak Aktif</h1>

        <p class="text-secondary mb-0">
            Kod QR bagi aset <strong>{{ $kodBangunan->bangunan?->asb_name }}</strong>
            sedang tidak aktif dan tidak menerima aduan baharu buat masa ini.
        </p>
    </div>
@endsection