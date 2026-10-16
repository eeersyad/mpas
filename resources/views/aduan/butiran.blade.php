@extends('layouts.utama')

@section('tajuk', 'Butiran Aduan')

@section('kandungan')
    @php
        $bolehProses = auth()->user()->mempunyaiPeranan(['SYSTEM', 'BPH'])
            && $aduan->cmp_status === 'BARU';
    @endphp

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('aduan.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>

            <p class="teks-kecil mb-1">BUTIRAN ADUAN</p>
            <h1 class="tajuk-halaman mb-2">
                {{ $aduan->bangunan?->asb_name ?? 'Aset Bangunan' }}
            </h1>

            <p class="teks-penerangan mb-0">
                Diterima pada {{ $aduan->cmp_received_date?->format('d/m/Y, h:i A') }}.
            </p>
        </div>

        <span class="badge text-bg-{{ $aduan->cmp_status === 'SELESAI' ? 'success' : 'primary' }} fs-6">
            {{ str_replace('_', ' ', $aduan->cmp_status) }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="kad-data p-4">
                <h2 class="h5 fw-bold mb-4">Maklumat Aduan</h2>

                <dl class="row mb-0">
                    <dt class="col-sm-4">No. Rujukan Aset</dt>
                    <dd class="col-sm-8">{{ $aduan->bangunan?->asb_ref_no ?? '-' }}</dd>

                    <dt class="col-sm-4">Jenis Kerosakan</dt>
                    <dd class="col-sm-8">{{ $aduan->cmp_damage_type ?? '-' }}</dd>

                    <dt class="col-sm-4">Penyewa</dt>
                    <dd class="col-sm-8">{{ $aduan->penyewa?->tnt_name ?? 'Orang Awam' }}</dd>

                    <dt class="col-sm-4">Keterangan</dt>
                    <dd class="col-sm-8 mb-0">{{ $aduan->cmp_note }}</dd>
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            @if ($bolehProses)
                <div class="kad-data p-4">
                    <h2 class="h5 fw-bold mb-3">Keputusan BPH</h2>

                    <form
                        method="POST"
                        action="{{ route('aduan.terima', $aduan) }}"
                        class="mb-3"
                    >
                        @csrf
                        @method('PUT')

                        <button type="submit" class="btn btn-utama w-100">
                            <i class="bi bi-check-circle me-2"></i>
                            Terima Aduan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('aduan.tolak', $aduan) }}">
                        @csrf
                        @method('PUT')

                        <label for="cmp_completion_note" class="form-label fw-semibold">
                            Sebab Penolakan <span class="text-danger">*</span>
                        </label>

                        <textarea
                            id="cmp_completion_note"
                            name="cmp_completion_note"
                            rows="4"
                            class="form-control mb-3 @error('cmp_completion_note') is-invalid @enderror"
                            placeholder="Contoh: Kerosakan berpunca daripada kecuaian penyewa."
                            required
                        >{{ old('cmp_completion_note') }}</textarea>

                        @error('cmp_completion_note')
                            <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-2"></i>
                            Tolak Aduan
                        </button>
                    </form>
                </div>
            @elseif (
                auth()->user()->mempunyaiPeranan(['SYSTEM', 'KJ']) &&
                $aduan->cmp_accepted_by !== null &&
                $aduan->cmp_status !== 'SELESAI'
            )
                <div class="kad-data p-4">
                    <h2 class="h5 fw-bold mb-1">Kemaskini Penyelenggaraan</h2>
                    <p class="text-secondary small mb-4">
                        Kemas kini status selepas kerja penyelenggaraan dijalankan.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('aduan.penyelenggaraan', $aduan) }}"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cmp_status" class="form-label fw-semibold">
                                Status Aduan <span class="text-danger">*</span>
                            </label>

                            <select
                                id="cmp_status"
                                name="cmp_status"
                                class="form-select @error('cmp_status') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Pilih Status --</option>

                                <option
                                    value="DALAM_PENYELENGGARAAN"
                                    @selected(old('cmp_status', $aduan->cmp_status) === 'DALAM_PENYELENGGARAAN')
                                >
                                    Dalam Penyelenggaraan
                                </option>

                                <option
                                    value="SELESAI"
                                    @selected(old('cmp_status', $aduan->cmp_status) === 'SELESAI')
                                >
                                    Selesai
                                </option>
                            </select>

                            @error('cmp_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cmp_maintenance_date" class="form-label fw-semibold">
                                Tarikh Penyelenggaraan
                            </label>

                            <input
                                type="date"
                                id="cmp_maintenance_date"
                                name="cmp_maintenance_date"
                                value="{{ old('cmp_maintenance_date', $aduan->cmp_maintenance_date?->format('Y-m-d')) }}"
                                class="form-control @error('cmp_maintenance_date') is-invalid @enderror"
                            >

                            @error('cmp_maintenance_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cmp_completion_note" class="form-label fw-semibold">
                                Catatan Penyelenggaraan
                            </label>

                            <textarea
                                id="cmp_completion_note"
                                name="cmp_completion_note"
                                rows="5"
                                class="form-control @error('cmp_completion_note') is-invalid @enderror"
                                placeholder="Nyatakan tindakan penyelenggaraan yang telah dijalankan."
                            >{{ old('cmp_completion_note', $aduan->cmp_completion_note) }}</textarea>

                            @error('cmp_completion_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-utama w-100">
                            <i class="bi bi-tools me-2"></i>
                            Simpan Kemaskini
                        </button>
                    </form>
                </div>
            @else
                <div class="kad-data p-4">
                    <h2 class="h5 fw-bold mb-3">Status Aduan</h2>

                    <p class="text-secondary mb-0">
                        @if ($aduan->cmp_status === 'SELESAI')
                            Aduan ini telah selesai diproses.
                        @elseif ($aduan->cmp_status === 'DITERIMA')
                            Aduan telah diterima dan sedang menunggu tindakan KJ.
                        @else
                            Aduan ini sedang menunggu keputusan BPH.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection