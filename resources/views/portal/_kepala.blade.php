@php
    $hubunganLaci = config('portal.hubungan');
@endphp

<header class="portal-bar">
    @isset($butangKembali)
        <a href="{{ $butangKembali }}" class="portal-bar-kembali" aria-label="Kembali"><i class="bi bi-arrow-left"></i></a>
    @else
        <button type="button" class="portal-bar-kembali portal-bar-menu" data-bs-toggle="offcanvas" data-bs-target="#laciPortal" aria-controls="laciPortal" aria-label="Buka menu"><i class="bi bi-list"></i></button>
    @endisset
    <a href="{{ route('portal.sewa') }}" class="portal-jenama"><img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS"></a>
    <span class="portal-bar-tajuk">{{ $tajukBar ?? 'Aset Untuk Disewa' }}</span>
    @isset($butangKongsi)
        <button type="button" class="portal-bar-ikon" data-kongsi aria-label="Kongsi"><i class="bi bi-share"></i></button>
    @endisset
    <nav class="portal-nav">
        <a href="{{ route('portal.sewa') }}" class="{{ request()->routeIs('portal.sewa') && request('paparan') !== 'peta' ? 'aktif' : '' }}"><i class="bi bi-grid"></i><span>Senarai Aset</span></a>
        <a href="{{ route('portal.sewa', ['paparan' => 'peta']) }}" class="{{ request('paparan') === 'peta' ? 'aktif' : '' }}"><i class="bi bi-map"></i><span>Peta</span></a>
        <a href="{{ route('aduan.status') }}"><i class="bi bi-search"></i><span>Status Aduan</span></a>
    </nav>
</header>

{{-- Laci navigasi sisi (mobile) --}}
<div class="offcanvas offcanvas-start laci-portal d-md-none" tabindex="-1" id="laciPortal" aria-labelledby="laciPortalTajuk">
    <div class="laci-kepala">
        <img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS">
        <button type="button" class="laci-tutup" data-bs-dismiss="offcanvas" aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
    </div>
    <p class="laci-sub" id="laciPortalTajuk">Portal Aset Awam &middot; Majlis Daerah Marang</p>

    <nav class="laci-menu">
        <a href="{{ route('portal.sewa') }}" class="{{ request()->routeIs('portal.sewa') && request('paparan') !== 'peta' ? 'aktif' : '' }}"><span class="laci-ikon"><i class="bi bi-buildings"></i></span><span>Aset Untuk Disewa</span><i class="bi bi-chevron-right"></i></a>
        <a href="{{ route('portal.sewa', ['paparan' => 'peta']) }}" class="{{ request('paparan') === 'peta' ? 'aktif' : '' }}"><span class="laci-ikon"><i class="bi bi-map"></i></span><span>Peta Aset</span><i class="bi bi-chevron-right"></i></a>
        <a href="{{ route('aduan.status') }}"><span class="laci-ikon"><i class="bi bi-clipboard-check"></i></span><span>Semak Status Aduan</span><i class="bi bi-chevron-right"></i></a>
    </nav>

    <div class="laci-bahagian">Hubungi Kami</div>
    <nav class="laci-menu">
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hubunganLaci['telefon']) }}"><span class="laci-ikon"><i class="bi bi-telephone"></i></span><span>{{ $hubunganLaci['telefon'] }}</span><i class="bi bi-chevron-right"></i></a>
        <a href="mailto:{{ $hubunganLaci['emel'] }}"><span class="laci-ikon"><i class="bi bi-envelope"></i></span><span class="text-truncate">{{ $hubunganLaci['emel'] }}</span><i class="bi bi-chevron-right"></i></a>
    </nav>

    <p class="laci-kaki">{{ $hubunganLaci['nama'] }}</p>
</div>
