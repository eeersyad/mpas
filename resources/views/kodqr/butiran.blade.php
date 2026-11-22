@extends('layouts.utama')

@section('tajuk', 'Butiran Kod QR')

@section('kandungan')
    {{-- Bar aplikasi (telefon) --}}
    <div class="bar-apl-halaman d-md-none">
        <a href="{{ route('kodqr.index') }}" class="butang-bar-apl" aria-label="Kembali ke senarai">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
        </a>

        <span class="tajuk-bar-apl">Kod QR</span>
    </div>

    <div class="d-none d-md-flex align-items-center gap-3 mb-4">
        <a href="{{ route('kodqr.index') }}" class="butang-kembali-laporan" aria-label="Kembali ke senarai Kod QR" title="Kembali ke Senarai"><i class="bi bi-arrow-left" aria-hidden="true"></i></a>

        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET · KOD QR</p>
            <h1 class="tajuk-halaman mb-2">Kod QR Aset Bangunan</h1>
            <p class="teks-penerangan mb-0">
                Jana, lihat dan kawal akses aduan awam bagi aset bangunan.
            </p>
        </div>
    </div>

    <div class="kad-maklumat-qr mb-4">
        <div class="ikon-maklumat-qr d-none d-md-grid">
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

        <span class="lencana-status-qr ms-md-auto d-none d-md-inline-flex">
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
                    {!! \App\Support\KodQr::svg($pautanAduan, 260) !!}
                </div>

                <p class="text-secondary small text-center mt-4 mb-0">
                    Orang awam atau penyewa boleh mengimbas kod ini untuk
                    mengemukakan aduan berkaitan aset.
                </p>
            </div>
        </div>

        {{-- Telefon: suis status & maklumat dalam senarai berkumpulan --}}
        <div class="col-12 d-md-none">
            <div class="kad-senarai-apl">
                <form method="POST" action="{{ route('kodqr.status', $bangunan) }}" class="baris-senarai-apl" data-suis-status-qr>
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="qr_status" value="{{ $kod->qr_status }}">

                    <label class="baris-suis-apl" for="suisStatusQr">
                        <span>
                            <strong>Terima aduan awam</strong>
                            <small data-teks-suis-qr>{{ $kod->qr_status === 'ACTIVE' ? 'Orang awam boleh membuat aduan' : 'Aduan awam ditutup' }}</small>
                        </span>

                        <span class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="suisStatusQr" @checked($kod->qr_status === 'ACTIVE')>
                        </span>
                    </label>
                </form>

                <div class="baris-senarai-apl">
                    <span>Tarikh dijana</span>
                    <strong>{{ $kod->generated_at?->format('d/m/Y, h:i A') ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7 d-none d-md-block">
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
                            <i class="bi bi-check2-circle me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bar tindakan melekat (telefon) --}}
    <div class="bar-tindakan-apl d-md-none">
        <button type="button" class="btn btn-outline-secondary" data-salin-pautan data-pautan="{{ $pautanAduan }}">
            <i class="bi bi-link-45deg me-1"></i>Salin pautan
        </button>

        <button type="button" class="btn btn-primary" data-kongsi-pautan data-pautan="{{ $pautanAduan }}" data-tajuk="Aduan {{ $bangunan->asb_name }}">
            <i class="bi bi-share me-1"></i>Kongsi
        </button>
    </div>

    <div class="ruang-fab d-md-none" aria-hidden="true"></div>
@endsection