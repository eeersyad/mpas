@extends('layouts.awam')

@section('tajuk', 'Semak Status Aduan')

@section('kandungan')

    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            <header class="awam-hero mb-4">
                <div class="awam-logo mb-3">
                    <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo MPAS">
                </div>

                <div class="maklumat-aset-awam">
                    <h1>Semak Status Aduan</h1>
                    <p class="alamat-aset-awam">
                        <i class="bi bi-building"></i>
                        <span>Majlis Daerah Marang · Aduan aset bangunan</span>
                    </p>
                </div>
            </header>

            <section class="kad-awam borang-awam">
                <div class="kepala-borang-awam mb-4">
                    <span class="ikon-borang-awam">
                        <i class="bi bi-search"></i>
                    </span>

                    <div>
                        <p class="teks-kecil mb-1">SEMAKAN ADUAN</p>
                        <h2 class="h5 fw-bold mb-1">Maklumat Aduan</h2>
                        <p class="text-secondary small mb-0">Masukkan no. rujukan dan alamat emel yang digunakan semasa membuat aduan.</p>
                    </div>
                </div>

                @if ($errors->has('rujukan') && old('rujukan'))
                    <div class="alert alert-danger d-flex align-items-start gap-2 small mb-4" role="alert">
                        <i class="bi bi-exclamation-circle-fill mt-1"></i>
                        <span>{{ $errors->first('rujukan') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('aduan.status.cari') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="rujukan" class="form-label fw-semibold">No. Rujukan Aduan <span class="text-danger">*</span></label>

                        <div class="medan-ikon-awam">
                            <i class="bi bi-hash" aria-hidden="true"></i>
                            <input id="rujukan" type="text" name="rujukan" class="form-control text-uppercase @error('rujukan') is-invalid @enderror" value="{{ old('rujukan') }}" placeholder="Contoh: ADU-2026-00042" maxlength="20" autocomplete="off" autocapitalize="characters" required>
                        </div>

                        @error('rujukan')
                            @if (! old('rujukan'))
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="emel" class="form-label fw-semibold">Alamat Emel <span class="text-danger">*</span></label>

                        <div class="medan-ikon-awam">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <input id="emel" type="email" name="emel" class="form-control @error('emel') is-invalid @enderror" value="{{ old('emel') }}" placeholder="nama@email.com" maxlength="150" autocomplete="email" inputmode="email" required>
                        </div>

                        @error('emel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-utama w-100 butang-hantar-awam">
                        <i class="bi bi-search me-2"></i>Semak Status
                    </button>
                </form>
            </section>

            <section class="kad-awam mt-4 nota-status-awam">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <strong>Di mana no. rujukan aduan?</strong>
                    <p class="mb-0">No. rujukan dihantar ke emel anda sejurus selepas aduan dibuat. Emel yang sama juga mengandungi pautan terus ke halaman status aduan.</p>
                </div>
            </section>
        </div>
    </div>
@endsection
