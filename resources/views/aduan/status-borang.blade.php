@extends('layouts.awam')

@section('tajuk', 'Semak Status Aduan')

@section('kandungan')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo MPAS" style="height: 56px;">
            </div>

            <section class="kad-awam p-4 p-md-5">
                <p class="small text-secondary fw-semibold mb-1">MAJLIS DAERAH MARANG</p>
                <h1 class="h4 fw-bold mb-2">Semak Status Aduan</h1>
                <p class="text-secondary small mb-4">
                    Masukkan no. rujukan aduan dan alamat emel yang digunakan semasa membuat aduan.
                </p>

                <form method="POST" action="{{ route('aduan.status.cari') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="rujukan" class="form-label fw-semibold">No. Rujukan Aduan</label>
                        <input id="rujukan" type="text" name="rujukan" class="form-control text-uppercase @error('rujukan') is-invalid @enderror" value="{{ old('rujukan') }}" placeholder="Contoh: ADU-2026-00042" maxlength="20" autocomplete="off" required>

                        @error('rujukan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="emel" class="form-label fw-semibold">Alamat Emel</label>
                        <input id="emel" type="email" name="emel" class="form-control @error('emel') is-invalid @enderror" value="{{ old('emel') }}" placeholder="nama@email.com" maxlength="150" autocomplete="email" inputmode="email" required>

                        @error('emel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-utama w-100 py-2">
                        <i class="bi bi-search me-2"></i>Semak Status
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
