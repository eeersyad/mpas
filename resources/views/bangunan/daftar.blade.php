@extends('layouts.utama')

@section('tajuk', 'Daftar Aset Bangunan')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Daftar Aset Bangunan</h1>
            <p class="teks-penerangan mb-0">
                Lengkapkan maklumat bangunan mengikut langkah yang disediakan.
            </p>
        </div>

        <a href="{{ route('bangunan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @include('bangunan.borang', [
        'bangunan' => null,
        'action' => route('bangunan.store'),
        'method' => 'POST',
        'butang' => 'Simpan Aset Bangunan',
    ])
@endsection