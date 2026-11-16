<header class="portal-bar">
    @isset($butangKembali)
        <a href="{{ $butangKembali }}" class="portal-bar-kembali" aria-label="Kembali"><i class="bi bi-arrow-left"></i></a>
    @endisset
    <a href="{{ route('portal.sewa') }}" class="portal-jenama"><img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS"></a>
    <span class="portal-bar-tajuk">{{ $tajukBar ?? 'Aset Untuk Disewa' }}</span>
    @isset($butangPeta)
        <a href="{{ $butangPeta['url'] }}" class="portal-bar-ikon" aria-label="{{ $butangPeta['label'] }}"><i class="bi {{ $butangPeta['ikon'] }}"></i></a>
    @endisset
    @isset($butangKongsi)
        <button type="button" class="portal-bar-ikon" data-kongsi aria-label="Kongsi"><i class="bi bi-share"></i></button>
    @endisset
    <nav class="portal-nav">
        <a href="{{ route('portal.sewa') }}" class="{{ request()->routeIs('portal.sewa') && request('paparan') !== 'peta' ? 'aktif' : '' }}"><i class="bi bi-grid"></i><span>Senarai Aset</span></a>
        <a href="{{ route('portal.sewa', ['paparan' => 'peta']) }}" class="{{ request('paparan') === 'peta' ? 'aktif' : '' }}"><i class="bi bi-map"></i><span>Peta</span></a>
        <a href="{{ route('aduan.status') }}"><i class="bi bi-search"></i><span>Status Aduan</span></a>
    </nav>
</header>
