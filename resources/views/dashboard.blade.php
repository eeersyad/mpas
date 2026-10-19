@extends('layouts.utama')

@section('tajuk', 'Dashboard')

@push('gaya')
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    >
@endpush

@section('kandungan')
    <div class="mb-4">
        <p class="teks-kecil mb-1">SELAMAT DATANG, {{ strtoupper($user->usr_name) }}</p>

        <h1 class="tajuk-halaman mb-2">Dashboard</h1>

        <p class="teks-penerangan mb-0">
            Ringkasan maklumat semasa Marang Property Asset System.
        </p>
    </div>

    <div class="row g-3 g-md-4">
        @if (in_array($user->usr_level, ['SYSTEM', 'BPH']))
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kad-ringkasan">
                    <div class="ikon-ringkasan hijau">
                        <i class="bi bi-buildings-fill"></i>
                    </div>

                    <div>
                        <p class="label-ringkasan">Aset Bangunan</p>
                        <h2 class="nilai-ringkasan">{{ $ringkasan['bangunan'] }}</h2>
                        <span class="teks-status">Jumlah berdaftar</span>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array($user->usr_level, ['SYSTEM', 'BPH', 'BP']))
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kad-ringkasan">
                    <div class="ikon-ringkasan biru">
                        <i class="bi bi-map-fill"></i>
                    </div>

                    <div>
                        <p class="label-ringkasan">Aset Tanah</p>
                        <h2 class="nilai-ringkasan">{{ $ringkasan['tanah'] }}</h2>
                        <span class="teks-status">Jumlah berdaftar</span>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array($user->usr_level, ['SYSTEM', 'BPH', 'KJ']))
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kad-ringkasan">
                    <div class="ikon-ringkasan jingga">
                        <i class="bi bi-tools"></i>
                    </div>

                    <div>
                        <p class="label-ringkasan">Aduan Aktif</p>
                        <h2 class="nilai-ringkasan">{{ $ringkasan['aduan'] }}</h2>
                        <span class="teks-status">Menunggu tindakan</span>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array($user->usr_level, ['SYSTEM', 'BPH']))
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kad-ringkasan">
                    <div class="ikon-ringkasan ungu">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div>
                        <p class="label-ringkasan">Pengguna Aktif</p>
                        <h2 class="nilai-ringkasan">{{ $ringkasan['pengguna'] }}</h2>
                        <span class="teks-status">Dalam sistem</span>
                    </div>
                </div>
            </div>
        @endif
        @if ($user->usr_level === 'KJ')
            <div class="col-12 col-md-6 col-xl-3">
                <div class="kad-ringkasan">
                    <div class="ikon-ringkasan biru">
                        <i class="bi bi-inbox-fill"></i>
                    </div>

                    <div>
                        <p class="label-ringkasan">Aduan Diterima</p>
                        <h2 class="nilai-ringkasan">{{ $ringkasan['diterima'] }}</h2>
                        <span class="teks-status">Menunggu tindakan KJ</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="kad-ringkasan">
                    <div class="ikon-ringkasan jingga">
                        <i class="bi bi-tools"></i>
                    </div>

                    <div>
                        <p class="label-ringkasan">Dalam Penyelenggaraan</p>
                        <h2 class="nilai-ringkasan">{{ $ringkasan['penyelenggaraan'] }}</h2>
                        <span class="teks-status">Kerja sedang berjalan</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if (in_array($user->usr_level, ['SYSTEM', 'BPH']))
        <div class="row g-3 g-md-4 mt-1">
            <div class="col-12 col-xl-6">
                <div class="kad-dashboard h-100">
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">ASET BANGUNAN</p>
                            <h2 class="tajuk-kad-dashboard mb-0">
                                Status Premis Bangunan
                            </h2>
                        </div>

                        <span class="lencana-dashboard">
                            <i class="bi bi-buildings-fill"></i>
                            {{ $ringkasan['bangunan'] }} aset
                        </span>
                    </div>

                    <div class="ruang-carta-bar">
                        <canvas id="cartaBangunan"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="kad-dashboard h-100">
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">ASET TANAH</p>
                            <h2 class="tajuk-kad-dashboard mb-0">
                                Jenis Hakmilik Tanah
                            </h2>
                        </div>

                        <span class="lencana-dashboard">
                            <i class="bi bi-map-fill"></i>
                            {{ $ringkasan['tanah'] }} aset
                        </span>
                    </div>

                    <div class="ruang-carta-bar">
                        <canvas id="cartaTanah"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5 align-self-start">
                <div class="kad-dashboard">
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">STATUS PENYELENGGARAAN</p>
                            <h2 class="tajuk-kad-dashboard mb-0">Skor Aset</h2>
                        </div>

                        <span class="lencana-dashboard">
                            <i class="bi bi-pie-chart-fill"></i>
                            Bangunan
                        </span>
                    </div>

                    <div class="ruang-carta-pie">
                        <canvas id="cartaSkorAset"></canvas>

                        <div class="nilai-skor-tengah">
                            <strong>{{ array_sum($skorAset['nilai']) }}</strong>
                            <span>Bangunan</span>
                        </div>
                    </div>

                    <div class="ringkasan-skor">
                        <div class="item-skor rendah">
                            <span>
                                <i class="bi bi-circle-fill"></i>
                                Rendah
                            </span>
                            <strong>{{ $skorAset['nilai'][0] }}</strong>
                        </div>

                        <div class="item-skor sederhana">
                            <span>
                                <i class="bi bi-circle-fill"></i>
                                Sederhana
                            </span>
                            <strong>{{ $skorAset['nilai'][1] }}</strong>
                        </div>

                        <div class="item-skor kritikal">
                            <span>
                                <i class="bi bi-circle-fill"></i>
                                Kritikal
                            </span>
                            <strong>{{ $skorAset['nilai'][2] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="kad-dashboard h-100">
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">LOKASI ASET</p>
                            <h2 class="tajuk-kad-dashboard mb-0">Peta Aset MPAS</h2>
                        </div>

                        <span class="lencana-dashboard">
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ count($lokasiAset) }} lokasi
                        </span>
                    </div>

                    <div id="petaAset" class="peta-aset"></div>

                    <div class="petunjuk-peta mt-3">
                        <span>
                            <i class="bi bi-buildings-fill text-success"></i>
                            Aset Bangunan
                        </span>

                        <span>
                            <i class="bi bi-map-fill text-primary"></i>
                            Aset Tanah
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($user->usr_level === 'BP')
        <div class="row g-3 g-md-4 mt-1">
            <div class="col-12 col-xl-6">
                <div class="kad-dashboard h-100">
                    <div class="d-flex justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">ASET TANAH</p>
                            <h2 class="tajuk-kad-dashboard mb-0">
                                Status Hakmilik Tanah
                            </h2>
                        </div>

                        <span class="lencana-dashboard">
                            <i class="bi bi-pie-chart-fill"></i>
                            {{ $ringkasan['tanah'] }} aset
                        </span>
                    </div>

                    <div class="ruang-carta-bar">
                        <canvas id="cartaStatusTanah"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="kad-dashboard h-100">
                    <div class="d-flex justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">ASET TANAH</p>
                            <h2 class="tajuk-kad-dashboard mb-0">
                                Jenis Hakmilik Tanah
                            </h2>
                        </div>

                        <span class="lencana-dashboard">
                            <i class="bi bi-bar-chart-fill"></i>
                            Analisis Semasa
                        </span>
                    </div>

                    <div class="ruang-carta-bar">
                        <canvas id="cartaJenisHakmilikTanah"></canvas>
                    </div>
                </div>
            </div>
        </div>

    @elseif ($user->usr_level === 'KJ')
        <div class="row g-3 g-md-4 mt-1">
            <div class="col-12 col-xl-5">
                <div class="kad-dashboard">
                    <div class="d-flex justify-content-between gap-2 mb-4">
                        <div>
                            <p class="teks-kecil mb-1">STATUS KERJA ADUAN</p>
                            <h2 class="tajuk-kad-dashboard mb-0">
                                Ringkasan Penyelenggaraan
                            </h2>
                        </div>
                    </div>

                    <div class="ruang-carta-pie">
                        <canvas id="cartaAduanKj"></canvas>

                        <div class="nilai-skor-tengah">
                            <strong>{{ $ringkasan['aduan'] }}</strong>
                            <span>Perlu Tindakan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="kad-dashboard h-100">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <p class="teks-kecil mb-1">TINDAKAN SEGERA</p>
                            <h2 class="tajuk-kad-dashboard mb-0">
                                Aduan Perlu Diberi Tindakan
                            </h2>
                        </div>

                        <a
                            href="{{ route('aduan.index') }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            Lihat Semua
                        </a>
                    </div>

                    <div class="senarai-tindakan-kj">
                        @forelse ($aduanTindakan as $aduan)
                            <a
                                href="{{ route('aduan.show', $aduan) }}"
                                class="item-tindakan-kj"
                            >
                                <span class="ikon-tindakan-kj">
                                    <i class="bi bi-tools"></i>
                                </span>

                                <span class="flex-grow-1 min-w-0">
                                    <strong class="d-block text-truncate">
                                        {{ $aduan->bangunan?->asb_name ?? '-' }}
                                    </strong>

                                    <small class="text-secondary d-block text-truncate">
                                        {{ $aduan->cmp_damage_type ?? 'Kerosakan tidak dinyatakan' }}
                                    </small>
                                </span>

                                <span class="badge {{ $aduan->cmp_status === 'DITERIMA' ? 'text-bg-info' : 'text-bg-warning' }}">
                                    {{ str_replace('_', ' ', $aduan->cmp_status) }}
                                </span>

                                <i class="bi bi-chevron-right text-secondary"></i>
                            </a>
                        @empty
                            <div class="kad-kosong py-4">
                                <div class="ilustrasi-kosong">
                                    <i class="bi bi-check2-circle"></i>
                                </div>

                                <h3>Tiada aduan aktif</h3>
                                <p>Semua aduan yang diterima telah selesai atau belum dihantar kepada KJ.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="kad-kosong mt-4">
            <div class="ilustrasi-kosong">
                <i class="bi bi-bar-chart-line"></i>
            </div>

            <h3>Analisis dashboard akan dikembangkan</h3>

            <p>
                Paparan analisis mengikut fungsi peranan akan ditambah kemudian.
            </p>
        </div>
    @endif
@endsection
@push('skrip')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin=""
    ></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dataBangunan = @json($cartaBangunan);
            const dataTanah = @json($cartaTanah);
            const dataSkor = @json($skorAset);
            const lokasiAset = @json($lokasiAset);
            const dataStatusTanah = @json($cartaStatusTanah);
            const dataJenisHakmilikTanah = @json($cartaJenisHakmilikTanah);
            const dataAduanKj = @json($cartaAduanKj);

            const binaCartaBar = (id, data, warna) => {
                const elemen = document.querySelector(id);

                if (!elemen) {
                    return;
                }

                new Chart(elemen, {
                    type: 'bar',
                    data: {
                        labels: data.label,
                        datasets: [{
                            label: 'Jumlah Aset',
                            data: data.nilai,
                            backgroundColor: warna.map((item) => `${item}B3`),
                            borderColor: warna,
                            borderWidth: 1.5,
                            hoverBackgroundColor: warna,
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 50,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                },
                                grid: {
                                    color: '#e7efee',
                                },
                            },
                            x: {
                                ticks: {
                                    maxRotation: 35,
                                    minRotation: 0,
                                },
                                grid: {
                                    display: false,
                                },
                            },
                        },
                    },
                });
            };

            binaCartaBar(
                '#cartaBangunan',
                dataBangunan,
                [
                    '#6FC7BD',
                    '#8FB7F7',
                    '#B9A3F5',
                    '#F6D58B',
                    '#F4B183',
                    '#EFA1B3',
                ]
            );

            binaCartaBar(
                '#cartaTanah',
                dataTanah,
                [
                    '#8FB7F7',
                    '#B9A3F5',
                    '#F6D58B',
                    '#6FC7BD',
                    '#EFA1B3',
                ]
            );
            binaCartaBar(
                '#cartaStatusTanah',
                dataStatusTanah,
                [
                    '#6FC7BD',
                    '#8FB7F7',
                    '#B9A3F5',
                    '#F6D58B',
                ]
            );

            binaCartaBar(
                '#cartaJenisHakmilikTanah',
                dataJenisHakmilikTanah,
                [
                    '#F4B183',
                    '#6FC7BD',
                    '#8FB7F7',
                    '#B9A3F5',
                    '#EFA1B3',
                ]
            );

            const elemenCartaSkor = document.querySelector('#cartaSkorAset');

            if (elemenCartaSkor) {
                new Chart(elemenCartaSkor, {
                    type: 'doughnut',
                    data: {
                        labels: dataSkor.label,
                        datasets: [{
                            data: dataSkor.nilai,
                            backgroundColor: [
                                '#22C55E',
                                '#EAB308',
                                '#EF4444',
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 4,
                            hoverOffset: 10,
                            cutout: '70%',
                            radius: '92%',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                    },
                });
            }

            const elemenCartaAduanKj = document.querySelector('#cartaAduanKj');

            if (elemenCartaAduanKj) {
                new Chart(elemenCartaAduanKj, {
                    type: 'doughnut',
                    data: {
                        labels: dataAduanKj.label,
                        datasets: [{
                            data: dataAduanKj.nilai,
                            backgroundColor: [
                                '#38BDF8',
                                '#F59E0B',
                                '#22C55E',
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 4,
                            hoverOffset: 10,
                            cutout: '70%',
                            radius: '92%',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                    },
                });
            }

            const elemenPeta = document.querySelector('#petaAset');

            if (!elemenPeta || typeof L === 'undefined') {
                return;
            }

            const peta = L.map('petaAset', {
                scrollWheelZoom: false,
            }).setView([5.205, 103.14], 11);

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }
            ).addTo(peta);

            const sempadan = [];

            const selamatkanTeks = (nilai) => String(nilai ?? '-')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            lokasiAset.forEach((aset) => {
                const bangunan = aset.jenis === 'Bangunan';

                const ikon = L.divIcon({
                    className: 'penanda-aset',
                    html: `
                        <span class="penanda-aset-bulat ${bangunan ? 'bangunan' : 'tanah'}">
                            <i class="bi ${bangunan ? 'bi-buildings-fill' : 'bi-map-fill'}"></i>
                        </span>
                    `,
                    iconSize: [38, 38],
                    iconAnchor: [19, 19],
                    popupAnchor: [0, -18],
                });

                const penanda = L.marker(
                    [aset.latitud, aset.longitud],
                    { icon: ikon }
                ).addTo(peta);

                penanda.bindPopup(`
                    <div class="popup-aset">
                        <small>${selamatkanTeks(aset.jenis)}</small>
                        <strong>${selamatkanTeks(aset.nama)}</strong>
                        <span>${selamatkanTeks(aset.rujukan)}</span>
                        <p>${selamatkanTeks(aset.lokasi)}</p>
                    </div>
                `);

                sempadan.push([aset.latitud, aset.longitud]);
            });

            if (sempadan.length > 0) {
                peta.fitBounds(sempadan, {
                    padding: [35, 35],
                    maxZoom: 15,
                });
            }
        });
    </script>
@endpush