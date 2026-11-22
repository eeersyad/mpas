@extends('layouts.utama')

@section('tajuk', 'Daftar Aset Bangunan')

@section('kandungan')
    {{-- Bar aplikasi (telefon) --}}
    <div class="bar-apl-halaman d-md-none">
        <a href="{{ route('bangunan.index') }}" class="butang-bar-apl" data-keluar-borang aria-label="Batal dan keluar">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </a>

        <span class="tajuk-bar-apl">Daftar Aset Bangunan</span>
    </div>

    <div class="d-none d-md-flex align-items-center gap-3 mb-4">
        <a href="{{ route('bangunan.index') }}" class="butang-kembali-laporan" data-keluar-borang aria-label="Kembali ke senarai aset bangunan" title="Kembali ke Senarai"><i class="bi bi-arrow-left" aria-hidden="true"></i></a>

        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Daftar Aset Bangunan</h1>
            <p class="teks-penerangan mb-0">
                Lengkapkan maklumat bangunan mengikut langkah yang disediakan.
            </p>
        </div>
    </div>

    @include('bangunan.borang', [
        'bangunan' => null,
        'action' => route('bangunan.store'),
        'method' => 'POST',
        'butang' => 'Simpan Aset Bangunan',
    ])
@endsection