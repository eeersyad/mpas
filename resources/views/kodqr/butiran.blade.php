@extends('layouts.utama')

@section('tajuk', 'Butiran Kod QR')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET · KOD QR</p>
            <h1 class="tajuk-halaman mb-2">Kod QR Aset Bangunan</h1>
            <p class="teks-penerangan mb-0">
                Jana, lihat dan kawal akses aduan awam bagi aset bangunan.
            </p>
        </div>

        <a href="{{ route('kodqr.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Senarai
        </a>
    </div>

    <div class="kad-maklumat-qr mb-4">
        <div class="ikon-maklumat-qr">
            <i class="bi bi-buildings"></i>
        </div>

        <div class="min-w-0">
            <span class="teks-kecil d-block mb-1">ASET BANGUNAN</span>
            <h2 class="h5 fw-bold mb-1">{{ $bangunan->asb_name }}</h2>
            <p class="mb-0 text-secondary small">
                <i class="bi bi-hash me-1"></i>{{ $bangunan->asb_ref_no }}
                @if ($bangunan->asb_city)
                    <span class="mx-1">·</span>
                    <i class="bi bi-geo-alt me-1"></i>{{ $bangunan->asb_city }}
                @endif
            </p>
        </div>

        <span class="lencana-status-qr ms-md-auto">
            <i class="bi {{ $kod->qr_status === 'ACTIVE' ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }}"></i>
            {{ $kod->qr_status === 'ACTIVE' ? 'QR Aktif' : 'QR Tidak Aktif' }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="kad-qr-utama h-100">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <span class="teks-kecil d-block mb-1">KOD QR ADUAN</span>
                        <h2 class="h5 fw-bold mb-0">Imbas untuk membuat aduan</h2>
                    </div>

                    <div class="ikon-qr-utama">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                </div>

                <div class="bungkus-qr-utama">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(260)
                        ->margin(1)
                        ->generate($pautanAduan) !!}
                </div>

                <p class="text-secondary small text-center mt-4 mb-0">
                    Orang awam atau penyewa boleh mengimbas kod ini untuk
                    mengemukakan aduan berkaitan aset.
                </p>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="kad-status-qr h-100">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <span class="teks-kecil d-block mb-1">PAUTAN AWAM</span>
                        <h2 class="h5 fw-bold mb-1">Akses borang aduan</h2>
                        <p class="text-secondary small mb-0">
                            Salin pautan ini jika anda mahu berkongsi borang aduan tanpa mengimbas QR.
                        </p>
                    </div>

                    <div class="ikon-status-qr">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                </div>

                <div class="pautan-qr mb-4">
                    <i class="bi bi-globe2"></i>

                    <span class="text-truncate">{{ $pautanAduan }}</span>

                    <button
                        type="button"
                        class="btn btn-sm btn-light"
                        data-salin-pautan
                        data-pautan="{{ $pautanAduan }}"
                        title="Salin pautan"
                    >
                        <i class="bi bi-copy"></i>
                    </button>
                </div>

                <div class="ringkasan-qr mb-4">
                    <div class="item-ringkasan-qr">
                        <span>Tarikh Dijana</span>
                        <strong>{{ $kod->generated_at?->format('d/m/Y, h:i A') ?? '-' }}</strong>
                    </div>

                    <div class="item-ringkasan-qr">
                        <span>Status Semasa</span>
                        <strong class="{{ $kod->qr_status === 'ACTIVE' ? 'teks-aktif' : 'teks-tidak-aktif' }}">
                            {{ $kod->qr_status === 'ACTIVE' ? 'Aktif' : 'Tidak Aktif' }}
                        </strong>
                    </div>
                </div>

                <div class="sempadan-status mb-4"></div>

                <form
                    method="POST"
                    action="{{ route('kodqr.status', $bangunan) }}"
                    data-borang-status-qr
                >
                    @csrf
                    @method('PUT')

                    <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                        <div class="flex-grow-1">
                            <label for="qr_status" class="label-penapis">
                                <i class="bi bi-toggle-on"></i> Status Kod QR
                            </label>

                            <select
                                id="qr_status"
                                name="qr_status"
                                class="form-select kawalan-penapis @error('qr_status') is-invalid @enderror"
                            >
                                <option value="ACTIVE" @selected($kod->qr_status === 'ACTIVE')>
                                    Aktif — Terima aduan awam
                                </option>

                                <option value="INACTIVE" @selected($kod->qr_status === 'INACTIVE')>
                                    Tidak Aktif — Tutup aduan awam
                                </option>
                            </select>

                            @error('qr_status')
                                <div class="invalid-feedback d-block mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-1"></i>Simpan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection