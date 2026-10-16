@extends('layouts.utama')

@section('tajuk', 'Tetapan Aduan Awam')

@section('kandungan')
    <div class="mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
        <p class="teks-penerangan mb-0">
            Konfigurasikan tempoh paparan status aduan pada pautan QR awam.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-3">
            <div class="tetapan-sisi">
                <p class="tetapan-sisi-tajuk">TETAPAN SISTEM</p>

                <nav class="nav flex-column gap-1">
                    <a href="{{ route('tetapan.rujukan') }}" class="tetapan-pautan">
                        <i class="bi bi-upc-scan"></i>
                        <span>Nombor Rujukan</span>
                    </a>

                    <a href="{{ route('tetapan.aduan') }}" class="tetapan-pautan aktif">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <span>Aduan Awam</span>
                    </a>

                    <span class="tetapan-pautan tidak-aktif">
                        <i class="bi bi-buildings"></i>
                        <span>Maklumat Organisasi</span>
                        <small>Akan Datang</small>
                    </span>

                    <span class="tetapan-pautan tidak-aktif">
                        <i class="bi bi-image"></i>
                        <span>Logo Sistem</span>
                        <small>Akan Datang</small>
                    </span>

                    <span class="tetapan-pautan tidak-aktif">
                        <i class="bi bi-palette-fill"></i>
                        <span>Paparan Sistem</span>
                        <small>Akan Datang</small>
                    </span>
                </nav>
            </div>
        </div>

        <div class="col-12 col-lg-9">
            <form method="POST" action="{{ route('tetapan.aduan.kemaskini') }}">
                @csrf
                @method('PUT')

                <div class="kad-data">
                    <div class="p-4 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-clock-history"></i>
                            </div>

                            <div>
                                <h2 class="h6 fw-bold mb-1">
                                    Tempoh Paparan Aduan Selesai
                                </h2>

                                <p class="mb-0 text-secondary small">
                                    Tetapkan berapa hari aduan yang telah selesai
                                    akan dipaparkan kepada orang awam melalui QR.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row align-items-end g-3">
                            <div class="col-12 col-md-6">
                                <label
                                    for="adu_tempoh_papar_selesai"
                                    class="form-label fw-semibold"
                                >
                                    Tempoh Paparan
                                </label>

                                <div class="input-group">
                                    <input
                                        type="number"
                                        min="1"
                                        max="365"
                                        id="adu_tempoh_papar_selesai"
                                        name="adu_tempoh_papar_selesai"
                                        value="{{ old(
                                            'adu_tempoh_papar_selesai',
                                            $tetapan?->adu_tempoh_papar_selesai ?? 30
                                        ) }}"
                                        class="form-control @error('adu_tempoh_papar_selesai') is-invalid @enderror"
                                        required
                                    >

                                    <span class="input-group-text">hari</span>
                                </div>

                                @error('adu_tempoh_papar_selesai')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border mb-0">
                                    <i class="bi bi-info-circle me-2 text-success"></i>
                                    Aduan selesai akan disorok daripada pautan awam
                                    selepas tempoh ini tamat, tetapi kekal direkodkan
                                    dalam sistem dalaman.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-utama px-4">
                        <i class="bi bi-check-lg me-2"></i>
                        Simpan Tetapan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection