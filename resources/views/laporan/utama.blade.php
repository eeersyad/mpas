@extends('layouts.utama')

@section('tajuk', 'Laporan')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">ANALISIS DAN EKSPORT</p>
            <h1 class="tajuk-halaman mb-2">Laporan Sistem</h1>
            <p class="teks-penerangan mb-0">
                Pilih jenis laporan untuk melihat data, membuat penapisan dan mengeksport rekod.
            </p>
        </div>

        <div class="lencana-laporan-utama">
            <i class="bi bi-file-earmark-bar-graph"></i>
            Laporan MPAS
        </div>
    </div>

    <div class="row g-3 g-md-4">
        @forelse ($laporan as $item)
            <div class="col-12 col-md-6">
                <a
                    href="{{ route('laporan.papar', $item['kod']) }}"
                    class="kad-pilihan-laporan tema-{{ $item['tema'] }} d-block text-decoration-none text-reset"
                >
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div class="ikon-pilihan-laporan">
                            <i class="bi {{ $item['ikon'] }}"></i>
                        </div>

                        <span class="lencana-tersedia">
                            <i class="bi bi-check-circle-fill"></i>
                            Tersedia
                        </span>
                    </div>

                    <div class="mt-4">
                        <h2 class="h5 fw-bold mb-2">{{ $item['tajuk'] }}</h2>

                        <p class="text-secondary small mb-4">
                            {{ $item['penerangan'] }}
                        </p>
                    </div>

                    <div class="d-flex align-items-end justify-content-between gap-3 pt-3 border-top">
                        <div>
                            <span class="teks-kecil d-block mb-1">REKOD SEMASA</span>
                            <strong class="nilai-laporan">
                                {{ number_format($item['jumlah']) }}
                            </strong>
                            <small class="text-secondary ms-1">
                                {{ $item['label_jumlah'] }}
                            </small>
                        </div>

                        <span class="teks-sedia-laporan">
                            Buka laporan
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="kad-kosong">
                    <div class="ilustrasi-kosong">
                        <i class="bi bi-file-earmark-x"></i>
                    </div>

                    <h3>Tiada laporan tersedia</h3>
                    <p>Anda tidak mempunyai akses kepada sebarang laporan buat masa ini.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection