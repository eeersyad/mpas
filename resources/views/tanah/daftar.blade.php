@extends('layouts.utama')

@section('tajuk', 'Daftar Aset Tanah')

@section('kandungan')
    {{-- Bar aplikasi (telefon) --}}
    <div class="bar-apl-halaman d-md-none">
        <a href="{{ route('tanah.index') }}" class="butang-bar-apl" data-keluar-borang aria-label="Batal dan keluar">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </a>

        <span class="tajuk-bar-apl">Daftar Aset Tanah</span>
    </div>

    <div class="d-none d-md-flex align-items-center gap-3 mb-4">
        <a href="{{ route('tanah.index') }}" class="butang-kembali-laporan" data-keluar-borang aria-label="Kembali ke senarai aset tanah" title="Kembali ke Senarai"><i class="bi bi-arrow-left" aria-hidden="true"></i></a>

        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Daftar Aset Tanah</h1>
            <p class="teks-penerangan mb-0">
                Lengkapkan maklumat aset tanah mengikut langkah yang disediakan.
            </p>
        </div>
    </div>

    @include('tanah.borang', [
        'tanah' => null,
        'action' => route('tanah.store'),
        'method' => 'POST',
        'butang' => 'Simpan Aset Tanah',
    ])
@endsection