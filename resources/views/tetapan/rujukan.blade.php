@extends('layouts.utama')

@section('tajuk', 'Tetapan Nombor Rujukan')

@section('kandungan')
    @php
        $tanah = $tetapan->get('TANAH');
        $bangunan = $tetapan->get('BANGUNAN');
    @endphp

    <div class="hero-tetapan mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
    </div>

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            <form
                method="POST"
                action="{{ route('tetapan.rujukan.kemaskini') }}"
                class="borang-tetapan"
            >
                @csrf
                @method('PUT')

                <div class="kad-data kad-tetapan mb-4">
                    <div class="kepala-kad-tetapan">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ikon-tetapan tanah">
                                <i class="bi bi-map-fill"></i>
                            </div>

                            <div>
                                <h2 class="h6 fw-bold mb-1">Nombor Rujukan Aset Tanah</h2>

                                @if (! $tanah)
                                    <p class="mb-0 text-secondary small">
                                        Tetapan belum dibuat.
                                    </p>
                                @elseif ($rujukanTerakhir['TANAH'])
                                    <p class="mb-0 text-secondary small">
                                        Rujukan terakhir dijana:
                                        <strong>{{ $rujukanTerakhir['TANAH'] }}</strong>
                                    </p>
                                @else
                                    <p class="mb-0 text-secondary small">
                                        Tiada rujukan telah dijana.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="isi-kad-tetapan">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label for="tanah_awalan" class="form-label fw-semibold">
                                    Awalan Rujukan
                                </label>

                                <input
                                    type="text"
                                    class="form-control @error('tanah_awalan') is-invalid @enderror"
                                    id="tanah_awalan"
                                    name="tanah_awalan"
                                    value="{{ old('tanah_awalan', $tanah?->ruj_awalan) }}"
                                    placeholder="Contoh: MPAS-TNH"
                                    required
                                >

                                @error('tanah_awalan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="tanah_nombor_mula" class="form-label fw-semibold">
                                    Nombor Mula
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    class="form-control @error('tanah_nombor_mula') is-invalid @enderror"
                                    id="tanah_nombor_mula"
                                    name="tanah_nombor_mula"
                                    value="{{ old('tanah_nombor_mula', $tanah?->ruj_nombor_mula ?? 1) }}"
                                    @readonly($tanah?->ruj_nombor_semasa !== null)
                                    required
                                >

                                @if ($tanah?->ruj_nombor_semasa !== null)
                                    <div class="form-text">
                                        Nombor mula tidak boleh diubah selepas rujukan dijana.
                                    </div>
                                @endif

                                @error('tanah_nombor_mula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="tanah_panjang_nombor" class="form-label fw-semibold">
                                    Panjang Nombor
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    max="10"
                                    class="form-control @error('tanah_panjang_nombor') is-invalid @enderror"
                                    id="tanah_panjang_nombor"
                                    name="tanah_panjang_nombor"
                                    value="{{ old('tanah_panjang_nombor', $tanah?->ruj_panjang_nombor ?? 6) }}"
                                    required
                                >

                                @error('tanah_panjang_nombor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kad-data kad-tetapan">
                    <div class="kepala-kad-tetapan">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-buildings-fill"></i>
                            </div>

                            <div>
                                <h2 class="h6 fw-bold mb-1">Nombor Rujukan Aset Bangunan</h2>

                                @if (! $bangunan)
                                    <p class="mb-0 text-secondary small">
                                        Tetapan belum dibuat.
                                    </p>
                                @elseif ($rujukanTerakhir['BANGUNAN'])
                                    <p class="mb-0 text-secondary small">
                                        Rujukan terakhir dijana:
                                        <strong>{{ $rujukanTerakhir['BANGUNAN'] }}</strong>
                                    </p>
                                @else
                                    <p class="mb-0 text-secondary small">
                                        Tiada rujukan telah dijana.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="isi-kad-tetapan">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label for="bangunan_awalan" class="form-label fw-semibold">
                                    Awalan Rujukan
                                </label>

                                <input
                                    type="text"
                                    class="form-control @error('bangunan_awalan') is-invalid @enderror"
                                    id="bangunan_awalan"
                                    name="bangunan_awalan"
                                    value="{{ old('bangunan_awalan', $bangunan?->ruj_awalan) }}"
                                    placeholder="Contoh: MPAS-BGN"
                                    required
                                >

                                @error('bangunan_awalan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="bangunan_nombor_mula" class="form-label fw-semibold">
                                    Nombor Mula
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    class="form-control @error('bangunan_nombor_mula') is-invalid @enderror"
                                    id="bangunan_nombor_mula"
                                    name="bangunan_nombor_mula"
                                    value="{{ old('bangunan_nombor_mula', $bangunan?->ruj_nombor_mula ?? 1) }}"
                                    @readonly($bangunan?->ruj_nombor_semasa !== null)
                                    required
                                >

                                @if ($bangunan?->ruj_nombor_semasa !== null)
                                    <div class="form-text">
                                        Nombor mula tidak boleh diubah selepas rujukan dijana.
                                    </div>
                                @endif

                                @error('bangunan_nombor_mula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="bangunan_panjang_nombor" class="form-label fw-semibold">
                                    Panjang Nombor
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    max="10"
                                    class="form-control @error('bangunan_panjang_nombor') is-invalid @enderror"
                                    id="bangunan_panjang_nombor"
                                    name="bangunan_panjang_nombor"
                                    value="{{ old('bangunan_panjang_nombor', $bangunan?->ruj_panjang_nombor ?? 6) }}"
                                    required
                                >

                                @error('bangunan_panjang_nombor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-utama butang-simpan-tetapan">
                        <i class="bi bi-check-lg me-2"></i>Simpan Tetapan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection