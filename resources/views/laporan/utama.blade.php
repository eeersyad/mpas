@extends('layouts.utama')

@section('tajuk', 'Laporan')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1 d-none d-md-block">ANALISIS DAN EKSPORT</p>
            <h1 class="tajuk-halaman mb-2">Laporan Sistem <span class="lencana-bilangan-tajuk d-none">{{ count($laporan) }} laporan</span></h1>
            <p class="teks-penerangan mb-0 d-none d-md-block">
                Pilih jenis laporan untuk melihat data, membuat penapisan dan mengeksport rekod.
            </p>
        </div>

    </div>

    {{-- Telefon: senarai gaya tetapan aplikasi --}}
    @if (count($laporan))
        <div class="kad-senarai-apl d-md-none">
            @foreach ($laporan as $item)
                <a href="{{ route('laporan.papar', $item['kod']) }}" class="baris-laporan-apl tema-{{ $item['tema'] }}">
                    <span class="ikon-pilihan-laporan"><i class="bi {{ $item['ikon'] }}"></i></span>

                    <span class="teks-baris-laporan">
                        <strong>{{ $item['tajuk'] }}</strong>
                        <small>{{ $item['penerangan'] }}</small>
                    </span>

                    <span class="bilangan-baris-laporan">{{ number_format($item['jumlah']) }}</span>
                    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Desktop: kad laporan mengikut kumpulan --}}
    @php
        $kumpulanLaporan = collect($laporan)->groupBy(fn ($item) => in_array($item['kod'], ['aduan', 'penyelenggaraan', 'pemeriksaan'], true) ? 'Aduan & Penyelenggaraan' : 'Aset & Penyewaan');
    @endphp

    @forelse ($kumpulanLaporan as $namaKumpulan => $senarai)
        <section class="kumpulan-laporan d-none d-md-block">
            <h2 class="tajuk-kumpulan-laporan">{{ $namaKumpulan }} <span>{{ $senarai->count() }}</span></h2>

            <div class="senarai-laporan-baris">
                @foreach ($senarai as $item)
                    <a href="{{ route('laporan.papar', $item['kod']) }}" class="baris-laporan-desktop tema-{{ $item['tema'] }}">
                        <span class="ikon-pilihan-laporan"><i class="bi {{ $item['ikon'] }}" aria-hidden="true"></i></span>

                        <span class="teks-baris-laporan-desktop">
                            <strong>{{ $item['tajuk'] }}</strong>
                            <small>{{ $item['penerangan'] }}</small>
                        </span>

                        <span class="nilai-baris-laporan {{ $item['kod'] === 'perjanjian_penyewa' && $item['jumlah'] > 0 ? 'perlu-tindakan' : '' }}">
                            <strong>{{ number_format($item['jumlah']) }}</strong>
                            <small>{{ $item['label_jumlah'] }}</small>
                        </span>

                        <i class="bi bi-chevron-right anak-panah-baris-laporan" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <div class="kad-kosong d-none d-md-block">
            <div class="ilustrasi-kosong">
                <i class="bi bi-file-earmark-x"></i>
            </div>

            <h3>Tiada laporan tersedia</h3>
            <p>Anda tidak mempunyai akses kepada sebarang laporan buat masa ini.</p>
        </div>
    @endforelse
@endsection