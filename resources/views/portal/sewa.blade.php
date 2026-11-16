@extends('layouts.awam')

@section('tajuk', 'Aset Untuk Disewa')
@section('kelas_body', 'halaman-portal')

@push('kepala')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('kandungan')
    @include('portal._kepala')

    <div class="mb-3">
        <h1 class="portal-tajuk text-start mb-1">Aset Untuk Disewa</h1>
        <p class="portal-subtajuk text-start mb-0">{{ $aset->count() }} aset dijumpai</p>
    </div>

    <form method="GET" action="{{ route('portal.sewa') }}" class="kad-awam portal-penapis mb-3">
        <input type="hidden" name="paparan" value="{{ $paparan }}">
        <div class="input-group mb-2">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="cari" value="{{ $cari }}" class="form-control" placeholder="Cari nama, lokasi atau no. lot">
        </div>
        <div class="row g-2">
            <div class="col-6 col-md-4">
                <select name="jenis" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua jenis</option>
                    <option value="bangunan" @selected($jenis === 'bangunan')>Bangunan</option>
                    <option value="tanah" @selected($jenis === 'tanah')>Tanah</option>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <select name="mukim" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua mukim</option>
                    @foreach ($senaraiMukim as $nilai => $label)
                        <option value="{{ $nilai }}" @selected($mukim === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-tema flex-fill">Cari</button>
                @if ($cari !== '' || $jenis || $mukim !== '')
                    <a href="{{ route('portal.sewa', ['paparan' => $paparan]) }}" class="btn btn-light flex-fill">Set semula</a>
                @endif
            </div>
        </div>
    </form>

    <div class="portal-togol mb-3">
        <a href="{{ request()->fullUrlWithQuery(['paparan' => 'senarai']) }}" class="{{ $paparan === 'senarai' ? 'aktif' : '' }}"><i class="bi bi-grid"></i> Senarai</a>
        <a href="{{ request()->fullUrlWithQuery(['paparan' => 'peta']) }}" class="{{ $paparan === 'peta' ? 'aktif' : '' }}"><i class="bi bi-map"></i> Peta</a>
    </div>

    @if ($aset->isEmpty())
        <div class="kad-awam text-center py-5 mb-4">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-2 mb-0 text-muted">Tiada aset tersedia untuk carian ini.</p>
        </div>
    @elseif ($paparan === 'peta')
        <div class="portal-peta-wrap mb-4">
            <div id="petaPortal" class="portal-peta" data-portal-peta data-aset='@json($aset->whereNotNull('lat')->values())'></div>
            @if ($aset->whereNull('lat')->count())
                <small class="text-muted d-block mt-2"><i class="bi bi-info-circle"></i> {{ $aset->whereNull('lat')->count() }} aset tiada koordinat lokasi dan tidak dipaparkan di peta.</small>
            @endif
        </div>
    @else
        <div class="portal-grid mb-4">
            @foreach ($aset as $item)
                <a href="{{ $item['url'] }}" class="portal-kad">
                    <div class="portal-kad-gambar">
                        @if (count($item['gambar_kecil']))
                            <img src="{{ $item['gambar_kecil'][0] }}" alt="{{ $item['tajuk'] }}" loading="lazy">
                        @else
                            <span class="portal-tiada-gambar"><i class="bi {{ $item['jenis'] === 'tanah' ? 'bi-geo-alt' : 'bi-building' }}"></i></span>
                        @endif
                        <span class="portal-lencana">{{ $item['jenis_label'] }}</span>
                        @if (count($item['gambar_kecil']) > 1)
                            <span class="portal-bil-gambar"><i class="bi bi-images"></i> {{ count($item['gambar_kecil']) }}</span>
                        @endif
                    </div>
                    <div class="portal-kad-badan">
                        <h2>{{ $item['tajuk'] }}</h2>
                        @if ($item['subtajuk'])<p class="portal-kad-sub">{{ $item['subtajuk'] }}</p>@endif
                        @if ($item['alamat'])<p class="portal-kad-alamat"><i class="bi bi-geo-alt"></i> {{ $item['alamat'] }}</p>@endif
                        <div class="portal-kad-kaki">
                            <span class="badge-tersedia"><i class="bi bi-check-circle"></i> Tersedia</span>
                            @if ($item['keluasan_teks'])<small>{{ $item['keluasan_teks'] }}</small>@endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @include('portal._hubungi')
@endsection

@push('skrip')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const el = document.querySelector('[data-portal-peta]');
            if (!el || !window.L) return;
            const aset = JSON.parse(el.dataset.aset || '[]');
            const jalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });
            const satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: 'Esri' });
            const peta = L.map(el, { layers: [jalan] }).setView([5.205, 103.205], 11);
            L.control.layers({ 'Jalan': jalan, 'Satelit': satelit }).addTo(peta);
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            const titik = [];
            aset.forEach((a) => {
                const ikon = L.divIcon({ className: 'portal-pin ' + a.jenis, html: '<i class="bi ' + (a.jenis === 'tanah' ? 'bi-geo-alt-fill' : 'bi-building-fill') + '"></i>', iconSize: [34, 34], iconAnchor: [17, 34], popupAnchor: [0, -30] });
                const gambar = a.gambar_kecil.length ? '<img src="' + esc(a.gambar_kecil[0]) + '" class="portal-popup-gambar" alt="">' : '';
                L.marker([a.lat, a.lng], { icon: ikon }).addTo(peta).bindPopup(
                    gambar + '<div class="portal-popup"><span class="portal-popup-jenis">' + esc(a.jenis_label) + '</span><strong>' + esc(a.tajuk) + '</strong>' +
                    '<div class="portal-popup-butang"><a href="' + esc(a.url) + '" class="btn btn-sm btn-tema">Butiran</a>' +
                    '<a href="' + esc(a.google_maps) + '" target="_blank" rel="noopener" class="btn btn-sm btn-light"><i class="bi bi-sign-turn-right"></i></a>' +
                    '<a href="' + esc(a.waze) + '" target="_blank" rel="noopener" class="btn btn-sm btn-light">Waze</a></div></div>',
                    { maxWidth: 240, minWidth: 220 }
                );
                titik.push([a.lat, a.lng]);
            });
            if (titik.length) peta.fitBounds(titik, { padding: [40, 40], maxZoom: 15 });
        })();
    </script>
@endpush
