@extends('layouts.utama')

@section('tajuk', 'Butiran Kod QR')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('kodqr.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>

            <p class="teks-kecil mb-1">KOD QR ASET</p>
            <h1 class="tajuk-halaman mb-2">{{ $bangunan->asb_name }}</h1>
            <p class="teks-penerangan mb-0">
                {{ $bangunan->asb_ref_no }}
            </p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="kad-data p-4 p-md-5 text-center">
                <div class="bg-white d-inline-block p-3 border rounded-4 mb-4">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(260)
                        ->margin(1)
                        ->generate($pautanAduan) !!}
                </div>

                <p class="text-secondary small mb-2">
                    Imbas QR untuk membuat aduan aset ini.
                </p>

                <div class="input-group">
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $pautanAduan }}"
                        readonly
                    >
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="kad-data p-4">
                <h2 class="h5 fw-bold mb-1">Status Kod QR</h2>
                <p class="text-secondary small mb-4">
                    QR tidak aktif tidak membenarkan aduan baharu daripada orang awam.
                </p>

                <form method="POST" action="{{ route('kodqr.status', $bangunan) }}">
                    @csrf
                    @method('PUT')

                    <label for="qr_status" class="form-label fw-semibold">
                        Status
                    </label>

                    <select
                        id="qr_status"
                        name="qr_status"
                        class="form-select mb-4 @error('qr_status') is-invalid @enderror"
                    >
                        <option value="ACTIVE" @selected($kod->qr_status === 'ACTIVE')>
                            Aktif
                        </option>

                        <option value="INACTIVE" @selected($kod->qr_status === 'INACTIVE')>
                            Tidak Aktif
                        </option>
                    </select>

                    @error('qr_status')
                        <div class="invalid-feedback d-block mb-3">
                            {{ $message }}
                        </div>
                    @enderror

                    <button type="submit" class="btn btn-utama">
                        <i class="bi bi-check-lg me-2"></i>
                        Simpan Status
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection