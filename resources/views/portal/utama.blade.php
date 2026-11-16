@extends('layouts.awam')

@section('tajuk', 'Portal Awam')
@section('kelas_body', 'halaman-portal')

@section('kandungan')
    <div class="awam-hero mb-4">
        <div class="awam-logo"><img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS"></div>
        <h1 class="portal-tajuk">Portal Aset Awam</h1>
        <p class="portal-subtajuk">{{ $hubungan['nama'] }} &middot; Pusat sehenti maklumat aset bangunan dan tanah</p>
    </div>

    <div class="portal-jubin-grid mb-4">
        <a href="{{ route('portal.sewa') }}" class="portal-jubin utama">
            <span class="portal-jubin-ikon"><i class="bi bi-buildings"></i></span>
            <span class="portal-jubin-teks"><strong>Aset Untuk Disewa</strong><small>{{ $bilTersedia }} aset bangunan &amp; tanah tersedia</small></span>
            <i class="bi bi-chevron-right"></i>
        </a>
        <a href="{{ route('portal.sewa', ['paparan' => 'peta']) }}" class="portal-jubin">
            <span class="portal-jubin-ikon"><i class="bi bi-map"></i></span>
            <span class="portal-jubin-teks"><strong>Peta Aset</strong><small>Lihat lokasi &amp; navigasi</small></span>
            <i class="bi bi-chevron-right"></i>
        </a>
        <a href="{{ route('aduan.status') }}" class="portal-jubin">
            <span class="portal-jubin-ikon"><i class="bi bi-search"></i></span>
            <span class="portal-jubin-teks"><strong>Semak Status Aduan</strong><small>Guna nombor rujukan aduan</small></span>
            <i class="bi bi-chevron-right"></i>
        </a>
        <div class="portal-jubin pasif">
            <span class="portal-jubin-ikon"><i class="bi bi-qr-code-scan"></i></span>
            <span class="portal-jubin-teks"><strong>Buat Aduan</strong><small>Imbas kod QR yang dilekatkan pada bangunan</small></span>
        </div>
    </div>

    @include('portal._hubungi')
@endsection
