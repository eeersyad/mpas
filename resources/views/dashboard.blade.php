@extends('layouts.utama')

@section('tajuk', 'Dashboard')

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
    </div>

    <div class="kad-kosong mt-4">
        <div class="ilustrasi-kosong">
            <i class="bi bi-bar-chart-line"></i>
        </div>

        <h3>Data ringkasan akan berkembang di sini</h3>

        <p>
            Carta aset dan analisis aduan akan dipaparkan selepas rekod mula didaftarkan.
        </p>
    </div>
@endsection