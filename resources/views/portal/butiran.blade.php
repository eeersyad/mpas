@extends('layouts.awam')

@section('tajuk', $aset['tajuk'])
@section('kelas_body', 'halaman-portal')

@push('kepala')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('kandungan')
    @include('portal._kepala')

    <a href="{{ url()->previous() !== url()->current() && str_contains(url()->previous(), '/awam/sewa') ? url()->previous() : route('portal.sewa') }}" class="portal-kembali"><i class="bi bi-arrow-left"></i> Kembali ke senarai</a>

    <div class="portal-galeri mb-3">
        @if (count($aset['gambar_besar']))
            <div class="portal-galeri-jalur" data-galeri>
                @foreach ($aset['gambar_besar'] as $i => $url)
                    <button type="button" class="portal-galeri-item" data-lightbox="{{ $i }}"><img src="{{ $url }}" alt="Gambar {{ $i + 1 }}" loading="{{ $i ? 'lazy' : 'eager' }}"></button>
                @endforeach
            </div>
            @if (count($aset['gambar_besar']) > 1)
                <span class="portal-bil-gambar"><i class="bi bi-images"></i> {{ count($aset['gambar_besar']) }} gambar &middot; leret</span>
            @endif
        @else
            <div class="portal-tiada-gambar besar"><i class="bi {{ $aset['jenis'] === 'tanah' ? 'bi-geo-alt' : 'bi-building' }}"></i><small>Tiada gambar</small></div>
        @endif
    </div>

    <section class="kad-awam mb-3">
        <span class="portal-lencana statik">{{ $aset['jenis_label'] }}</span>
        <h1 class="portal-butiran-tajuk">{{ $aset['tajuk'] }}</h1>
        @if ($aset['subtajuk'])<p class="portal-kad-sub mb-2">{{ $aset['subtajuk'] }}</p>@endif
        <span class="badge-tersedia"><i class="bi bi-check-circle"></i> Tersedia untuk disewa</span>

        <dl class="portal-maklumat mt-3 mb-0">
            @if ($aset['alamat'])<div><dt><i class="bi bi-geo-alt"></i> Lokasi</dt><dd>{{ $aset['alamat'] }}</dd></div>@endif
            @if ($aset['mukim'])<div><dt><i class="bi bi-pin-map"></i> Mukim</dt><dd>{{ $aset['mukim'] }}</dd></div>@endif
            @if ($aset['keluasan_teks'])<div><dt><i class="bi bi-arrows-angle-expand"></i> Keluasan</dt><dd>{{ $aset['keluasan_teks'] }}</dd></div>@endif
        </dl>
    </section>

    @if ($aset['lat'])
        <section class="kad-awam mb-3">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-map text-tema me-1"></i> Lokasi &amp; Navigasi</h2>
            <div id="petaButiran" class="portal-peta kecil mb-3" data-lat="{{ $aset['lat'] }}" data-lng="{{ $aset['lng'] }}"></div>
            <div class="row g-2">
                <div class="col-6"><a href="{{ $aset['google_maps'] }}" target="_blank" rel="noopener" class="btn btn-tema w-100"><i class="bi bi-google me-1"></i> Google Maps</a></div>
                <div class="col-6"><a href="{{ $aset['waze'] }}" target="_blank" rel="noopener" class="btn btn-outline-tema w-100"><i class="bi bi-sign-turn-right me-1"></i> Waze</a></div>
                <div class="col-12"><button type="button" class="btn btn-light w-100" data-salin="{{ $aset['lat'] }}, {{ $aset['lng'] }}"><i class="bi bi-copy me-1"></i> Salin koordinat ({{ number_format($aset['lat'], 5) }}, {{ number_format($aset['lng'], 5) }})</button></div>
            </div>
        </section>
    @endif

    @include('portal._hubungi')

    @if (count($aset['gambar_besar']))
        <div class="portal-lightbox" data-lightbox-panel hidden>
            <button type="button" class="portal-lightbox-tutup" data-lightbox-tutup aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
            <div class="portal-lightbox-jalur" data-lightbox-jalur>
                @foreach ($aset['gambar_besar'] as $url)
                    <div class="portal-lightbox-item"><img src="{{ $url }}" alt="" loading="lazy"></div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('skrip')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const el = document.getElementById('petaButiran');
            if (el && window.L) {
                const lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
                const jalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });
                const satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: 'Esri' });
                const peta = L.map(el, { layers: [jalan], scrollWheelZoom: false }).setView([lat, lng], 16);
                L.control.layers({ 'Jalan': jalan, 'Satelit': satelit }).addTo(peta);
                L.marker([lat, lng]).addTo(peta);
            }

            document.querySelectorAll('[data-salin]').forEach((b) => b.addEventListener('click', async () => {
                try { await navigator.clipboard.writeText(b.dataset.salin); } catch (e) { return; }
                const asal = b.innerHTML;
                b.innerHTML = '<i class="bi bi-check2 me-1"></i> Disalin';
                setTimeout(() => (b.innerHTML = asal), 1800);
            }));

            const panel = document.querySelector('[data-lightbox-panel]');
            if (!panel) return;
            const jalur = panel.querySelector('[data-lightbox-jalur]');
            const tutup = () => { panel.hidden = true; document.body.style.overflow = ''; };
            document.querySelectorAll('[data-lightbox]').forEach((b) => b.addEventListener('click', () => {
                panel.hidden = false;
                document.body.style.overflow = 'hidden';
                jalur.scrollTo({ left: jalur.clientWidth * parseInt(b.dataset.lightbox, 10), behavior: 'instant' });
            }));
            panel.querySelector('[data-lightbox-tutup]').addEventListener('click', tutup);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !panel.hidden) tutup(); });
        })();
    </script>
@endpush
