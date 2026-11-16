@extends('layouts.awam')

@section('tajuk', 'Aset Untuk Disewa')
@section('kelas_body', 'halaman-portal' . ($paparan === 'peta' ? ' portal-mod-peta' : ''))

@push('kepala')
    @include('portal._pwa')
@endpush

@php
    $pautan = fn (array $ubah) => route('portal.sewa', array_filter(array_merge(['cari' => $cari, 'jenis' => $jenis, 'mukim' => $mukim, 'paparan' => $paparan === 'peta' ? 'peta' : null], $ubah), fn ($v) => $v !== null && $v !== ''));
    $asetPeta = $aset->whereNotNull('lat')->values();
@endphp

@section('kandungan')
    @include('portal._kepala', ['tajukBar' => ''])

    <div class="portal-carian-baris" data-portal-melekat>
        <form method="GET" action="{{ route('portal.sewa') }}" class="portal-carian" role="search">
            @if ($jenis)<input type="hidden" name="jenis" value="{{ $jenis }}">@endif
            @if ($mukim !== '')<input type="hidden" name="mukim" value="{{ $mukim }}">@endif
            @if ($paparan === 'peta')<input type="hidden" name="paparan" value="peta">@endif
            <i class="bi bi-search"></i>
            <input type="search" name="cari" value="{{ $cari }}" placeholder="Cari nama, lokasi atau no. lot" enterkeyhint="search" autocomplete="off">
            @if ($cari !== '')
                <a href="{{ $pautan(['cari' => null]) }}" class="portal-carian-padam" aria-label="Padam carian"><i class="bi bi-x-circle-fill"></i></a>
            @endif
        </form>
        <button type="button" class="portal-butang-penapis d-md-none" data-bs-toggle="offcanvas" data-bs-target="#lembaranPenapis" aria-label="Penapis"><i class="bi bi-sliders"></i>@if ($mukim !== '')<span class="portal-titik-aktif"></span>@endif</button>
        <form method="GET" action="{{ route('portal.sewa') }}" class="d-none d-md-block">
            @foreach (['cari' => $cari, 'jenis' => $jenis, 'paparan' => $paparan === 'peta' ? 'peta' : null] as $k => $v)
                @if ($v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
            @endforeach
            <select name="mukim" class="form-select portal-pilih-mukim" onchange="this.form.submit()">
                <option value="">Semua mukim</option>
                @foreach ($senaraiMukim as $nilai => $label)
                    <option value="{{ $nilai }}" @selected($mukim === (string) $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="portal-atas">
        <nav class="portal-tab">
            <a href="{{ $pautan(['jenis' => null]) }}" class="{{ ! $jenis ? 'aktif' : '' }}">Semua</a>
            <a href="{{ $pautan(['jenis' => 'bangunan']) }}" class="{{ $jenis === 'bangunan' ? 'aktif' : '' }}">Bangunan</a>
            <a href="{{ $pautan(['jenis' => 'tanah']) }}" class="{{ $jenis === 'tanah' ? 'aktif' : '' }}">Tanah</a>
        </nav>

        <div class="portal-kiraan">
            <span><strong>{{ $aset->count() }}</strong> aset tersedia @if ($mukim !== '')&middot; {{ $senaraiMukim[$mukim] ?? $mukim }} <a href="{{ $pautan(['mukim' => null]) }}" class="portal-buang-penapis" aria-label="Buang penapis mukim"><i class="bi bi-x-circle-fill"></i></a>@endif</span>
            <div class="portal-togol d-none d-md-inline-flex">
                <a href="{{ $pautan(['paparan' => null]) }}" class="{{ $paparan === 'senarai' ? 'aktif' : '' }}"><i class="bi bi-grid"></i> Senarai</a>
                <a href="{{ $pautan(['paparan' => 'peta']) }}" class="{{ $paparan === 'peta' ? 'aktif' : '' }}"><i class="bi bi-map"></i> Peta</a>
            </div>
        </div>
    </div>

    @if ($aset->isEmpty())
        <div class="portal-kosong">
            <i class="bi bi-search"></i>
            <p class="mb-1 fw-bold">Tiada aset dijumpai</p>
            <small class="text-muted">Cuba ubah carian atau penapis.</small>
            @if ($cari !== '' || $jenis || $mukim !== '')
                <a href="{{ route('portal.sewa', $paparan === 'peta' ? ['paparan' => 'peta'] : []) }}" class="btn btn-tema btn-sm mt-3">Set semula penapis</a>
            @endif
        </div>
    @elseif ($paparan === 'peta')
        <div class="portal-peta-wrap">
            <div id="petaPortal" class="portal-peta" data-portal-peta data-aset='@json($asetPeta)'></div>
            <button type="button" class="portal-peta-lokasi" data-lokasi-saya aria-label="Lokasi saya"><i class="bi bi-crosshair"></i></button>
            @if ($aset->count() > $asetPeta->count())
                <span class="portal-nota-peta"><i class="bi bi-info-circle"></i> {{ $aset->count() - $asetPeta->count() }} aset tiada lokasi</span>
            @endif
            <div class="portal-pin-kad" data-pin-kad hidden></div>
        </div>
    @else
        <div class="portal-grid">
            @foreach ($aset as $item)
                <a href="{{ $item['url'] }}" class="portal-kad">
                    <div class="portal-kad-gambar">
                        @if (count($item['gambar_kecil']))
                            <img src="{{ $item['gambar_kecil'][0] }}" alt="{{ $item['tajuk'] }}" loading="lazy">
                        @else
                            <span class="portal-tiada-gambar"><img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS"></span>
                        @endif
                        <span class="portal-lencana">{{ $item['jenis_label'] }}</span>
                        @if (count($item['gambar_kecil']) > 1)
                            <span class="portal-bil-gambar"><i class="bi bi-images"></i> {{ count($item['gambar_kecil']) }}</span>
                        @endif
                    </div>
                    <div class="portal-kad-badan">
                        <h2>{{ $item['tajuk'] }}</h2>
                        @if ($item['alamat'])<p class="portal-kad-alamat"><i class="bi bi-geo-alt"></i> {{ $item['alamat'] }}</p>@endif
                        <div class="portal-kad-kaki">
                            <span class="badge-tersedia"><i class="bi bi-check-circle-fill"></i> Tersedia</span>
                            @if ($item['keluasan_teks'])<small>{{ $item['keluasan_teks'] }}</small>@endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @include('portal._hubungi')

    <button type="button" class="portal-fab" data-bs-toggle="offcanvas" data-bs-target="#lembaranHubungi" aria-label="Hubungi"><i class="bi bi-telephone-fill"></i></button>

    <div class="offcanvas offcanvas-bottom panel-penapis-mobile" tabindex="-1" id="lembaranPenapis" aria-labelledby="lembaranPenapisTajuk">
        <div class="pemegang-panel" aria-hidden="true"></div>
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="lembaranPenapisTajuk">Penapis</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <form method="GET" action="{{ route('portal.sewa') }}" class="borang-penapis-mobile">
            @foreach (['cari' => $cari, 'jenis' => $jenis, 'paparan' => $paparan === 'peta' ? 'peta' : null] as $k => $v)
                @if ($v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
            @endforeach
            <div class="offcanvas-body">
                <fieldset class="kumpulan-pill">
                    <legend>Mukim</legend>
                    <div class="senarai-pill">
                        <input type="radio" class="btn-check" name="mukim" id="ppMukimSemua" value="" autocomplete="off" @checked($mukim === '')>
                        <label class="pill-pilihan" for="ppMukimSemua">Semua</label>
                        @foreach ($senaraiMukim as $nilai => $label)
                            <input type="radio" class="btn-check" name="mukim" id="ppMukim{{ $loop->index }}" value="{{ $nilai }}" autocomplete="off" @checked($mukim === (string) $nilai)>
                            <label class="pill-pilihan" for="ppMukim{{ $loop->index }}">{{ $label }}</label>
                        @endforeach
                    </div>
                </fieldset>
            </div>
            <div class="kaki-penapis-mobile">
                <a href="{{ $pautan(['mukim' => null]) }}" class="btn btn-light flex-fill">Set semula</a>
                <button type="submit" class="btn btn-tema flex-fill">Terapkan</button>
            </div>
        </form>
    </div>
@endsection

@push('skrip')
    @include('portal._skrip-asas')
    <script>
        (() => {
            const bar = document.querySelector('[data-portal-melekat]');
            if (!bar) return;
            const kemaskini = () => {
                const atas = parseFloat(getComputedStyle(bar).top) || 0;
                bar.classList.toggle('sedang-melekat', getComputedStyle(bar).position === 'sticky' && bar.getBoundingClientRect().top <= atas + 1 && window.scrollY > 0);
            };
            window.addEventListener('scroll', kemaskini, { passive: true });
            kemaskini();
        })();
    </script>
    <script>
        (() => {
            const el = document.querySelector('[data-portal-peta]');
            if (!el || !window.L) return;

            const aset = JSON.parse(el.dataset.aset || '[]');
            const logoMpas = @json(asset('images/logo-mpas-2.png'));
            const kad = document.querySelector('[data-pin-kad]');
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            const jalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });
            const satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: 'Esri' });
            const peta = L.map(el, { layers: [jalan], zoomControl: false }).setView([5.205, 103.205], 11);
            L.control.zoom({ position: 'topright' }).addTo(peta);
            L.control.layers({ 'Jalan': jalan, 'Satelit': satelit }, null, { position: 'topright' }).addTo(peta);

            let pinAktif = null;
            const tutupKad = () => { kad.hidden = true; pinAktif?.getElement()?.classList.remove('aktif'); pinAktif = null; };

            const titik = aset.map((a) => {
                const ikon = L.divIcon({ className: 'portal-penanda', html: '<svg viewBox="0 0 32 42" width="32" height="42"><path d="M16 1C7.7 1 1 7.6 1 15.8 1 27 16 41 16 41s15-14 15-25.2C31 7.6 24.3 1 16 1z" fill="' + (a.jenis === 'tanah' ? '#c77d16' : '#168a7e') + '" stroke="#fff" stroke-width="2"/><circle cx="16" cy="15.5" r="5.5" fill="#fff"/></svg>', iconSize: [32, 42], iconAnchor: [16, 41] });
                const pin = L.marker([a.lat, a.lng], { icon: ikon }).addTo(peta);
                pin.on('click', () => {
                    pinAktif?.getElement()?.classList.remove('aktif');
                    pinAktif = pin;
                    pin.getElement()?.classList.add('aktif');
                    const gambar = a.gambar_kecil.length
                        ? '<img src="' + esc(a.gambar_kecil[0]) + '" alt="">'
                        : '<span class="portal-tiada-gambar"><img src="' + logoMpas + '" alt="MPAS"></span>';
                    kad.innerHTML =
                        '<button type="button" class="portal-pin-kad-tutup" aria-label="Tutup"><i class="bi bi-x"></i></button>' +
                        '<a href="' + esc(a.url) + '" class="portal-pin-kad-isi"><div class="portal-pin-kad-gambar">' + gambar + '</div>' +
                        '<div class="portal-pin-kad-teks"><span class="portal-popup-jenis">' + esc(a.jenis_label) + '</span><strong>' + esc(a.tajuk) + '</strong>' +
                        (a.alamat ? '<small>' + esc(a.alamat) + '</small>' : '') + '</div></a>' +
                        '<div class="portal-pin-kad-butang"><a href="' + esc(a.url) + '" class="btn btn-tema btn-sm">Lihat butiran</a>' +
                        '<a href="' + esc(a.google_maps) + '" target="_blank" rel="noopener" class="btn btn-outline-tema btn-sm"><i class="bi bi-sign-turn-right"></i> Navigasi</a></div>';
                    kad.hidden = false;
                    kad.querySelector('.portal-pin-kad-tutup').addEventListener('click', tutupKad);
                    peta.panTo([a.lat, a.lng]);
                });
                return [a.lat, a.lng];
            });

            peta.on('click', tutupKad);
            if (titik.length) peta.fitBounds(titik, { padding: [50, 50], maxZoom: 15 });

            let pinSaya = null;
            document.querySelector('[data-lokasi-saya]')?.addEventListener('click', () => {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition((p) => {
                    const ll = [p.coords.latitude, p.coords.longitude];
                    pinSaya?.remove();
                    pinSaya = L.circleMarker(ll, { radius: 8, color: '#fff', weight: 3, fillColor: '#2563eb', fillOpacity: 1 }).addTo(peta);
                    peta.setView(ll, 14);
                }, () => Swal.fire({ icon: 'info', title: 'Lokasi tidak dapat dikesan', text: 'Sila benarkan akses lokasi pada pelayar.', confirmButtonColor: '#168A7E' }), { enableHighAccuracy: true, timeout: 10000 });
            });
        })();
    </script>
@endpush
