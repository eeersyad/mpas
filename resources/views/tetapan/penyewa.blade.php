@extends('layouts.utama')

@section('tajuk', 'Tetapan Penyewa')

@section('kandungan')
    <div class="hero-tetapan mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
    </div>

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            <form method="POST" action="{{ route('tetapan.penyewa.kemaskini') }}" class="borang-tetapan">
                @csrf
                @method('PUT')

                <div class="kad-data kad-tetapan">
                    <div class="kepala-kad-tetapan">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-calendar2-week"></i>
                            </div>

                            <div>
                                <h2 class="h6 fw-bold mb-1">Tempoh Hampir Tamat Perjanjian</h2>
                                <p class="mb-0 text-secondary small">
                                    Tetapkan tempoh awal untuk mengenal pasti penyewa yang
                                    perjanjian mereka bakal tamat dalam laporan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="isi-kad-tetapan">
                        <div class="row align-items-end g-3">
                            <div class="col-12 col-md-6">
                                <label for="tpy_tempoh_hampir_tamat_perjanjian" class="form-label fw-semibold">
                                    Tempoh Hampir Tamat
                                </label>

                                <div class="input-group">
                                    <input
                                        type="number"
                                        min="1"
                                        max="365"
                                        id="tpy_tempoh_hampir_tamat_perjanjian"
                                        name="tpy_tempoh_hampir_tamat_perjanjian"
                                        value="{{ old('tpy_tempoh_hampir_tamat_perjanjian', $tetapan?->tpy_tempoh_hampir_tamat_perjanjian ?? 30) }}"
                                        class="form-control @error('tpy_tempoh_hampir_tamat_perjanjian') is-invalid @enderror"
                                        required
                                    >
                                    <span class="input-group-text">hari</span>
                                </div>

                                @error('tpy_tempoh_hampir_tamat_perjanjian')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="makluman-tetapan mb-0">
                                    <i class="bi bi-info-circle me-2 text-success"></i>
                                    Laporan akan menyenaraikan penyewa aktif yang akan tamat
                                    dalam tempoh ini atau yang perjanjian mereka sudah tamat.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-utama butang-simpan-tetapan">
                        <i class="bi bi-check-lg me-2"></i>
                        Simpan Tetapan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
