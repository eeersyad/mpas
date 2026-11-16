<header class="portal-bar">
    <a href="{{ route('portal.index') }}" class="portal-jenama"><img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS"></a>
    <nav class="portal-nav">
        <a href="{{ route('portal.sewa') }}" class="{{ request()->routeIs('portal.sewa*') && request('paparan') !== 'peta' ? 'aktif' : '' }}"><i class="bi bi-buildings"></i><span>Aset</span></a>
        <a href="{{ route('portal.sewa', ['paparan' => 'peta']) }}" class="{{ request('paparan') === 'peta' ? 'aktif' : '' }}"><i class="bi bi-map"></i><span>Peta</span></a>
        <a href="{{ route('aduan.status') }}"><i class="bi bi-search"></i><span>Status Aduan</span></a>
    </nav>
</header>
