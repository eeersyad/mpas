@extends('layouts.awam')

@section('tajuk', $aset['tajuk'])
@section('kelas_body', 'halaman-portal portal-mod-butiran')

@push('kepala')
    @include('portal._pwa')
    <meta property="og:title" content="{{ $aset['tajuk'] }}">
    <meta property="og:description" content="{{ $aset['jenis_label'] }} tersedia untuk disewa{{ $aset['alamat'] ? ' · ' . $aset['alamat'] : '' }}">
    @if (count($aset['gambar_besar']))
        <meta property="og:image" content="{{ $aset['gambar_besar'][0] }}">
    @endif
@endpush

@php
    $sebelum = url()->previous();
    $kembali = $sebelum !== url()->current() && str_contains($sebelum, '/awam') && ! str_contains($sebelum, '/awam/bangunan') ? $sebelum : route('portal.sewa');
@endphp

@section('kandungan')
    @include('portal._kepala', ['butangKembali' => $kembali, 'butangKongsi' => true, 'tajukBar' => 'Bangunan'])

    <a href="{{ $kembali }}" class="portal-kembali d-none d-md-inline-flex"><i class="bi bi-arrow-left"></i> Kembali ke senarai</a>

    <div class="portal-butiran">
        <div class="portal-hero">
            @if (count($aset['gambar_besar']))
                <div class="portal-hero-jalur" data-galeri>
                    @foreach ($aset['gambar_besar'] as $i => $url)
                        <button type="button" class="portal-hero-item" data-lightbox="{{ $i }}"><img src="{{ $url }}" alt="Gambar {{ $i + 1 }}" loading="{{ $i ? 'lazy' : 'eager' }}"></button>
                    @endforeach
                </div>
                @if (count($aset['gambar_besar']) > 1)
                    <div class="portal-hero-titik" data-titik>
                        @foreach ($aset['gambar_besar'] as $i => $url)
                            <span class="{{ $i === 0 ? 'aktif' : '' }}"></span>
                        @endforeach
                    </div>
                    <span class="portal-hero-bil" data-bil>1 / {{ count($aset['gambar_besar']) }}</span>
                    <button type="button" class="portal-anak-panah sebelum" data-anak-panah="-1" data-sasaran="[data-galeri]" aria-label="Gambar sebelum"><i class="bi bi-chevron-left"></i></button>
                    <button type="button" class="portal-anak-panah selepas" data-anak-panah="1" data-sasaran="[data-galeri]" aria-label="Gambar seterusnya"><i class="bi bi-chevron-right"></i></button>
                @endif
            @else
                <div class="portal-tiada-gambar besar"><img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS"></div>
            @endif
        </div>

        <div class="portal-butiran-isi">
            <section class="kad-awam portal-kad-utama">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <span class="portal-lencana statik">{{ $aset['jenis_label'] }}</span>
                    <button type="button" class="btn btn-sm btn-light d-none d-md-inline-flex align-items-center gap-1" data-kongsi><i class="bi bi-share"></i> Kongsi</button>
                </div>
                <h1 class="portal-butiran-tajuk">{{ $aset['tajuk'] }}</h1>
                @if ($aset['subtajuk'])<p class="portal-kad-sub mb-2">{{ $aset['subtajuk'] }}</p>@endif
                <span class="badge-tersedia"><i class="bi bi-check-circle-fill"></i> Tersedia untuk disewa</span>

                <div class="portal-maklumat">
                    @if ($aset['alamat'])
                        <div class="portal-maklumat-baris"><span class="portal-baris-ikon"><i class="bi bi-geo-alt"></i></span><div><small>Lokasi</small><strong>{{ $aset['alamat'] }}</strong></div></div>
                    @endif
                    @if ($aset['mukim'])
                        <div class="portal-maklumat-baris"><span class="portal-baris-ikon"><i class="bi bi-pin-map"></i></span><div><small>Mukim</small><strong>{{ $aset['mukim'] }}</strong></div></div>
                    @endif
                    @if ($aset['kadar_sewa'])
                        <div class="portal-maklumat-baris"><span class="portal-baris-ikon"><i class="bi bi-cash-coin"></i></span><div><small>Kadar Sewa</small><strong>{{ $aset['kadar_sewa'] }}</strong></div></div>
                    @endif
                    @if ($aset['iklan_mula'] && $aset['iklan_tamat'])
                        <div class="portal-maklumat-baris"><span class="portal-baris-ikon"><i class="bi bi-calendar-event"></i></span><div><small>Tempoh Iklan</small><strong>{{ $aset['iklan_mula'] }} hingga {{ $aset['iklan_tamat'] }}</strong></div></div>
                    @endif
                    @if ($aset['keluasan_teks'])
                        <div class="portal-maklumat-baris"><span class="portal-baris-ikon"><i class="bi bi-arrows-angle-expand"></i></span><div><small>Keluasan</small><strong>{{ $aset['keluasan_teks'] }}</strong></div></div>
                    @endif
                </div>
            </section>

            @if ($aset['lat'])
                <section class="kad-awam">
                    <h2 class="portal-seksyen-tajuk"><i class="bi bi-map"></i> Lokasi</h2>
                    <div class="portal-peta-mini-wrap">
                        <div id="petaButiran" class="portal-peta kecil" data-lat="{{ $aset['lat'] }}" data-lng="{{ $aset['lng'] }}"></div>
                        <a href="{{ $aset['google_maps'] }}" target="_blank" rel="noopener" class="portal-peta-mini-pautan"><i class="bi bi-box-arrow-up-right"></i> Buka di Google Maps</a>
                    </div>
                    <a href="{{ $aset['google_maps'] }}" target="_blank" rel="noopener" class="btn btn-tema w-100 mt-3 d-none d-md-block"><i class="bi bi-sign-turn-right me-1"></i> Navigasi dengan Google Maps</a>
                </section>
            @endif

            @include('portal._hubungi')
        </div>
    </div>

    <div class="portal-bar-tindakan">
        <button type="button" class="btn btn-outline-tema" data-bs-toggle="offcanvas" data-bs-target="#lembaranHubungi"><i class="bi bi-telephone-fill"></i> Hubungi</button>
        @if ($aset['google_maps'])
            <a href="{{ $aset['google_maps'] }}" target="_blank" rel="noopener" class="btn btn-tema"><i class="bi bi-sign-turn-right-fill"></i> Navigasi</a>
        @else
            <button type="button" class="btn btn-tema" disabled><i class="bi bi-sign-turn-right-fill"></i> Tiada lokasi</button>
        @endif
    </div>

    @if (count($aset['gambar_besar']))
        <div class="portal-lightbox" data-lightbox-panel hidden>
            <button type="button" class="portal-lightbox-tutup" data-lightbox-tutup aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
            <div class="portal-lightbox-jalur" data-lightbox-jalur>
                @foreach ($aset['gambar_besar'] as $url)
                    <div class="portal-lightbox-item"><img src="{{ $url }}" alt="" loading="lazy"></div>
                @endforeach
            </div>
            @if (count($aset['gambar_besar']) > 1)
                <button type="button" class="portal-anak-panah sebelum gelap" data-anak-panah="-1" data-sasaran="[data-lightbox-jalur]" aria-label="Gambar sebelum"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="portal-anak-panah selepas gelap" data-anak-panah="1" data-sasaran="[data-lightbox-jalur]" aria-label="Gambar seterusnya"><i class="bi bi-chevron-right"></i></button>
            @endif
        </div>
    @endif
@endsection

@push('skrip')
    @include('portal._skrip-asas')
    <script>
        (() => {
            const el = document.getElementById('petaButiran');
            if (el && window.L) {
                const lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
                const jalan = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });
                const satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: 'Esri' });
                const peta = L.map(el, { layers: [jalan], scrollWheelZoom: false }).setView([lat, lng], 16);
                L.control.layers({ 'Jalan': jalan, 'Satelit': satelit }).addTo(peta);
                L.marker([lat, lng], { icon: L.divIcon({ className: 'portal-penanda', html: '<svg viewBox="0 0 32 42" width="32" height="42"><path d="M16 1C7.7 1 1 7.6 1 15.8 1 27 16 41 16 41s15-14 15-25.2C31 7.6 24.3 1 16 1z" fill="#168a7e" stroke="#fff" stroke-width="2"/><circle cx="16" cy="15.5" r="5.5" fill="#fff"/></svg>', iconSize: [32, 42], iconAnchor: [16, 41] }) }).addTo(peta);
            }

            const galeri = document.querySelector('[data-galeri]');
            const titik = document.querySelectorAll('[data-titik] span');
            const bil = document.querySelector('[data-bil]');
            if (galeri && titik.length) {
                galeri.addEventListener('scroll', () => {
                    const i = Math.round(galeri.scrollLeft / galeri.clientWidth);
                    titik.forEach((t, n) => t.classList.toggle('aktif', n === i));
                    if (bil) bil.textContent = (i + 1) + ' / ' + titik.length;
                }, { passive: true });
            }

            // Anak panah kiri/kanan (berpusing ke awal/akhir)
            document.querySelectorAll('[data-anak-panah]').forEach((b) => b.addEventListener('click', (e) => {
                e.stopPropagation();
                const jalur = document.querySelector(b.dataset.sasaran);
                if (!jalur) return;
                const jumlah = jalur.children.length;
                const semasa = Math.round(jalur.scrollLeft / jalur.clientWidth);
                const seterusnya = (semasa + parseInt(b.dataset.anakPanah, 10) + jumlah) % jumlah;
                jalur.scrollTo({ left: jalur.clientWidth * seterusnya, behavior: 'smooth' });
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
            document.addEventListener('keydown', (e) => {
                if (panel.hidden) return;
                if (e.key === 'Escape') tutup();
                if (e.key === 'ArrowLeft') panel.querySelector('[data-anak-panah="-1"]')?.click();
                if (e.key === 'ArrowRight') panel.querySelector('[data-anak-panah="1"]')?.click();
            });
        })();
    </script>
@endpush
