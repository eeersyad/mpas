@extends('layouts.utama')

@section('tajuk', 'Tetapan Nombor Rujukan')

@section('kandungan')
    @php
        $tanah = $tetapan->get('TANAH');
        $bangunan = $tetapan->get('BANGUNAN');
    @endphp

    <div class="hero-tetapan mb-4 d-none d-md-block">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
    </div>

    @include('tetapan._bar-apl', ['tajuk' => 'Nombor Rujukan', 'borang' => 'borangTetapanRujukan'])

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3 d-none d-md-block">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            <form method="POST" action="{{ route('tetapan.rujukan.kemaskini') }}" id="borangTetapanRujukan" class="borang-tetapan" data-pantau-ubah>
                @csrf
                @method('PUT')

                {{-- ===================== TELEFON: senarai gaya Settings ===================== --}}
                <div class="d-md-none tetapan-apl">
                    <div class="kad-pratonton-apl">
                        <p>Rujukan seterusnya</p>

                        <div class="baris-pratonton-apl">
                            <span><i class="bi bi-map-fill"></i> Tanah</span>
                            <strong data-pratonton-mobile="tanah">-</strong>
                        </div>

                        <div class="baris-pratonton-apl">
                            <span><i class="bi bi-buildings-fill"></i> Bangunan</span>
                            <strong data-pratonton-mobile="bangunan">-</strong>
                        </div>
                    </div>

                    @foreach (['tanah' => ['Tanah', $tanah, 'TANAH'], 'bangunan' => ['Bangunan', $bangunan, 'BANGUNAN']] as $jenis => [$labelJenis, $modelJenis, $kunciJenis])
                        <p class="label-kumpulan-apl">Aset {{ $labelJenis }}</p>

                        <div class="kad-senarai-apl">
                            <button type="button" class="baris-tetapan-medan" data-sunting-medan="{{ $jenis }}_awalan" data-label="Awalan Rujukan {{ $labelJenis }}">
                                <span class="label-medan-apl">Awalan</span>
                                <strong data-nilai-medan="{{ $jenis }}_awalan">-</strong>
                                <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                            </button>

                            @if ($modelJenis?->ruj_nombor_semasa !== null)
                                <div class="baris-tetapan-medan dikunci">
                                    <span class="label-medan-apl">Nombor mula</span>
                                    <strong data-nilai-medan="{{ $jenis }}_nombor_mula">-</strong>
                                    <i class="bi bi-lock-fill anak-panah-apl" aria-label="Dikunci"></i>
                                </div>
                            @else
                                <button type="button" class="baris-tetapan-medan" data-sunting-medan="{{ $jenis }}_nombor_mula" data-label="Nombor Mula {{ $labelJenis }}">
                                    <span class="label-medan-apl">Nombor mula</span>
                                    <strong data-nilai-medan="{{ $jenis }}_nombor_mula">-</strong>
                                    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                                </button>
                            @endif

                            <div class="baris-tetapan-medan">
                                <span class="label-medan-apl">Panjang nombor</span>

                                <div class="stepper-apl" data-stepper-proksi="{{ $jenis }}_panjang_nombor">
                                    <button type="button" data-arah="-1" aria-label="Kurangkan panjang nombor"><i class="bi bi-dash-lg"></i></button>
                                    <strong data-nilai-medan="{{ $jenis }}_panjang_nombor">-</strong>
                                    <button type="button" data-arah="1" aria-label="Tambah panjang nombor"><i class="bi bi-plus-lg"></i></button>
                                </div>
                            </div>
                        </div>

                        @foreach (['awalan', 'nombor_mula', 'panjang_nombor'] as $medanRalat)
                            @error($jenis . '_' . $medanRalat)
                                <p class="ralat-kumpulan-apl">{{ $message }}</p>
                            @enderror
                        @endforeach

                        <p class="nota-kumpulan-apl">
                            @if (! $modelJenis)
                                Tetapan belum dibuat.
                            @elseif ($rujukanTerakhir[$kunciJenis])
                                Rujukan terakhir dijana: {{ $rujukanTerakhir[$kunciJenis] }}
                            @else
                                Tiada rujukan telah dijana.
                            @endif
                            @if ($modelJenis?->ruj_nombor_semasa !== null)
                                Nombor mula dikunci selepas rujukan dijana.
                            @endif
                        </p>
                    @endforeach
                </div>

                <div class="kad-data kad-tetapan mb-4 d-none d-md-block">
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

                        <div class="pratonton-rujukan mt-3" data-pratonton-rujukan="tanah" data-nombor-semasa="{{ $tanah?->ruj_nombor_semasa }}">
                            <span><i class="bi bi-eye me-1"></i>Rujukan seterusnya</span>
                            <strong data-pratonton-teks>-</strong>
                        </div>
                    </div>
                </div>

                <div class="kad-data kad-tetapan d-none d-md-block">
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

                        <div class="pratonton-rujukan mt-3" data-pratonton-rujukan="bangunan" data-nombor-semasa="{{ $bangunan?->ruj_nombor_semasa }}">
                            <span><i class="bi bi-eye me-1"></i>Rujukan seterusnya</span>
                            <strong data-pratonton-teks>-</strong>
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

    @include('tetapan._lembaran-sunting')
@endsection