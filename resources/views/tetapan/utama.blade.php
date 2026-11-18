@extends('layouts.utama')

@section('tajuk', 'Tetapan')

@section('kandungan')
    <div class="hero-tetapan mb-4">
        <p class="teks-kecil mb-1 d-none d-md-block">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
        <p class="teks-penerangan mb-0 d-none d-md-block">Urus konfigurasi dan akses sistem.</p>
    </div>

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3 d-none d-md-block">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            @foreach ($kumpulan as $namaKumpulan => $senarai)
                <p class="label-kumpulan-apl">{{ $namaKumpulan }}</p>

                <div class="kad-senarai-apl mb-4">
                    @foreach ($senarai as $item)
                        @if ($item['url'])
                            <a href="{{ $item['url'] }}" class="baris-laporan-apl baris-tetapan-apl tema-{{ $item['tema'] }}">
                                <span class="ikon-pilihan-laporan"><i class="bi {{ $item['ikon'] }}"></i></span>

                                <span class="teks-baris-laporan">
                                    <strong>{{ $item['tajuk'] }}</strong>
                                    <small>{{ $item['penerangan'] }}</small>
                                </span>

                                <span class="nilai-tetapan-apl">{{ $item['nilai'] }}</span>
                                <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                            </a>
                        @else
                            <div class="baris-laporan-apl baris-tetapan-apl tidak-aktif">
                                <span class="ikon-pilihan-laporan"><i class="bi {{ $item['ikon'] }}"></i></span>

                                <span class="teks-baris-laporan">
                                    <strong>{{ $item['tajuk'] }}</strong>
                                    <small>{{ $item['penerangan'] }}</small>
                                </span>

                                <span class="lencana-akan-datang">Akan datang</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endsection
