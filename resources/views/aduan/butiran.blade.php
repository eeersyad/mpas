@extends('layouts.utama')

@section('tajuk', 'Butiran Aduan')

@section('kandungan')
    @php
        $user = auth()->user();

        $bolehProses = $user->mempunyaiPeranan(['SYSTEM', 'BPH'])
            && $aduan->cmp_status === 'BARU';

        $bolehSenggara = $user->mempunyaiPeranan(['SYSTEM', 'KJ'])
            && $aduan->cmp_accepted_by !== null
            && $aduan->cmp_status !== 'SELESAI';

        $statusKelas = match ($aduan->cmp_status) {
            'BARU' => 'baru',
            'DITERIMA' => 'diterima',
            'DALAM_PENYELENGGARAAN' => 'penyelenggaraan',
            'SELESAI' => 'selesai',
            default => 'baru',
        };

        $statusIkon = match ($aduan->cmp_status) {
            'BARU' => 'bi-hourglass-split',
            'DITERIMA' => 'bi-clipboard2-check-fill',
            'DALAM_PENYELENGGARAAN' => 'bi-tools',
            'SELESAI' => 'bi-check-circle-fill',
            default => 'bi-info-circle-fill',
        };

        $ditolak = $aduan->cmp_status === 'SELESAI'
            && $aduan->cmp_accepted_by === null;
    @endphp

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('aduan.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Senarai
            </a>

            <p class="teks-kecil mb-1">PENGURUSAN ADUAN</p>
            <h1 class="tajuk-halaman mb-2">Butiran Aduan</h1>
            <p class="teks-penerangan mb-0">
                Semak maklumat aduan dan rekodkan tindakan yang berkaitan.
            </p>
        </div>

        <span class="lencana-status-aduan besar {{ $statusKelas }}">
            <i class="bi {{ $statusIkon }}"></i>
            {{ \App\Support\Paparan::label($aduan->cmp_status) }}
        </span>
    </div>

    <div class="hero-aduan mb-4">
        <div class="hero-aduan-ikon {{ $statusKelas }}">
            <i class="bi bi-buildings-fill"></i>
        </div>

        <div class="min-w-0 flex-grow-1">
            <span class="teks-kecil d-block mb-1">ASET BANGUNAN TERLIBAT</span>
            <h2 class="h5 fw-bold mb-1">
                {{ $aduan->bangunan?->asb_name ?? 'Aset Bangunan' }}
            </h2>

            <p class="mb-0 text-secondary small">
                <i class="bi bi-hash me-1"></i>
                {{ $aduan->bangunan?->asb_ref_no ?? '-' }}

                <span class="mx-1">·</span>

                <i class="bi bi-calendar-event me-1"></i>
                {{ $aduan->cmp_received_date?->format('d/m/Y, h:i A') ?? '-' }}
            </p>
        </div>

        <div class="hero-aduan-pengadu">
            <span class="teks-kecil d-block mb-1">PENGADU</span>
            <strong>{{ $aduan->penyewa?->tnt_name ?? 'Orang Awam' }}</strong>
            <small class="d-block text-secondary">
                {{ $aduan->tnt_id ? 'Penyewa aset' : 'Aduan awam' }}
            </small>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="kad-butiran-aduan mb-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="ikon-seksyen-aduan">
                        <i class="bi bi-clipboard2-data"></i>
                    </div>

                    <div>
                        <h2 class="h6 fw-bold mb-0">Maklumat Aduan</h2>
                        <small class="text-secondary">Butiran yang dihantar oleh pengadu.</small>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="item-butiran-aduan">
                            <span>Jenis Kerosakan</span>
                            <strong>
                                {{ \App\Support\Paparan::label($aduan->cmp_damage_type) }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="item-butiran-aduan">
                            <span>Jenis Pengadu</span>
                            <strong>{{ $aduan->tnt_id ? 'Penyewa' : 'Orang Awam' }}</strong>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="catatan-aduan">
                            <span>
                                <i class="bi bi-chat-left-text me-1"></i>
                                Keterangan Aduan
                            </span>

                            <p class="mb-0">
                                {{ $aduan->cmp_note ?: 'Tiada keterangan tambahan diberikan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($aduan->gambar->isNotEmpty())
                <div class="kad-butiran-aduan">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="ikon-seksyen-aduan">
                            <i class="bi bi-images"></i>
                        </div>

                        <div>
                            <h2 class="h6 fw-bold mb-0">Gambar Kerosakan</h2>
                            <small class="text-secondary">
                                {{ $aduan->gambar->count() }} gambar dilampirkan oleh pengadu.
                            </small>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach ($aduan->gambar as $gambar)
                            <div class="col-6 col-md-4">
                                <a
                                    href="{{ asset('storage/' . $gambar->cmpimg_path) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="gambar-aduan-bingkai"
                                >
                                    <img
                                        src="{{ asset('storage/' . $gambar->cmpimg_path) }}"
                                        alt="Gambar kerosakan aduan"
                                    >

                                    <span>
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-5">
            <div class="kad-tindakan-aduan">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="ikon-tindakan-aduan {{ $statusKelas }}">
                        <i class="bi bi-signpost-split-fill"></i>
                    </div>

                    <div>
                        <h2 class="h6 fw-bold mb-0">Status dan Tindakan</h2>
                        <small class="text-secondary">
                            Rekod perjalanan aduan semasa.
                        </small>
                    </div>
                </div>

                <div class="garis-masa-aduan mb-4">
                    <div class="langkah-aduan selesai">
                        <div class="bulatan-langkah-aduan">
                            <i class="bi bi-send-check-fill"></i>
                        </div>

                        <div>
                            <strong>Aduan Diterima</strong>
                            <span>
                                {{ $aduan->cmp_received_date?->format('d/m/Y, h:i A') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="langkah-aduan {{ $aduan->cmp_accepted_at ? 'selesai' : ($ditolak ? 'selesai' : '') }}">
                        <div class="bulatan-langkah-aduan">
                            <i class="bi {{ $ditolak ? 'bi-x-circle-fill' : 'bi-clipboard2-check-fill' }}"></i>
                        </div>

                        <div>
                            <strong>{{ $ditolak ? 'Aduan Ditolak' : 'Semakan BPH' }}</strong>
                            <span>
                                @if ($ditolak)
                                    Aduan tidak diteruskan untuk penyelenggaraan.
                                @elseif ($aduan->cmp_accepted_at)
                                    {{ $aduan->cmp_accepted_at->format('d/m/Y, h:i A') }}
                                @else
                                    Menunggu keputusan BPH.
                                @endif
                            </span>
                        </div>
                    </div>

                    @if (! $ditolak)
                        <div class="langkah-aduan {{ $aduan->cmp_maintenance_date ? 'selesai' : '' }}">
                            <div class="bulatan-langkah-aduan">
                                <i class="bi bi-tools"></i>
                            </div>

                            <div>
                                <strong>Penyelenggaraan</strong>
                                <span>
                                    {{ $aduan->cmp_maintenance_date?->format('d/m/Y') ?? 'Belum direkodkan.' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="langkah-aduan {{ $aduan->cmp_status === 'SELESAI' ? 'selesai' : '' }}">
                        <div class="bulatan-langkah-aduan">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                        <div>
                            <strong>Selesai</strong>
                            <span>
                                {{ $aduan->cmp_completed_at?->format('d/m/Y, h:i A') ?? 'Belum selesai.' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($bolehProses)
                    <div class="panel-tindakan-bph">
                        <h3 class="h6 fw-bold mb-1">Keputusan BPH</h3>
                        <p class="text-secondary small mb-3">
                            Terima aduan untuk dihantar kepada KJ atau tolak jika berpunca daripada pihak pengadu.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('aduan.terima', $aduan) }}"
                            class="mb-3"
                            data-terima-aduan
                        >
                            @csrf
                            @method('PUT')

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Terima Aduan
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('aduan.tolak', $aduan) }}"
                            data-tolak-aduan
                        >
                            @csrf
                            @method('PUT')

                            <label for="cmp_completion_note" class="label-penapis">
                                <i class="bi bi-chat-square-text"></i>
                                Sebab Penolakan <span class="text-danger">*</span>
                            </label>

                            <textarea
                                id="cmp_completion_note"
                                name="cmp_completion_note"
                                rows="4"
                                class="form-control kawalan-penapis mb-3 @error('cmp_completion_note') is-invalid @enderror"
                                placeholder="Contoh: Kerosakan berpunca daripada kecuaian penyewa."
                                required
                            >{{ old('cmp_completion_note') }}</textarea>

                            @error('cmp_completion_note')
                                <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-2"></i>Tolak Aduan
                            </button>
                        </form>
                    </div>
                @elseif ($bolehSenggara)
                    <div class="panel-tindakan-kj">
                        <h3 class="h6 fw-bold mb-1">Kemaskini Penyelenggaraan</h3>
                        <p class="text-secondary small mb-3">
                            Rekodkan tindakan KJ selepas kerja penyelenggaraan dijalankan.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('aduan.penyelenggaraan', $aduan) }}"
                            data-kemaskini-aduan
                        >
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="cmp_status" class="label-penapis">
                                    <i class="bi bi-clipboard-check"></i>
                                    Status Aduan <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="cmp_status"
                                    name="cmp_status"
                                    class="form-select kawalan-penapis @error('cmp_status') is-invalid @enderror"
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
                            </div>

                            <div class="mb-3">
                                <label for="cmp_maintenance_date" class="label-penapis">
                                    <i class="bi bi-calendar-check"></i>
                                    Tarikh Penyelenggaraan
                                </label>

                                <input
                                    type="date"
                                    id="cmp_maintenance_date"
                                    name="cmp_maintenance_date"
                                    value="{{ old('cmp_maintenance_date', $aduan->cmp_maintenance_date?->format('Y-m-d')) }}"
                                    class="form-control kawalan-penapis @error('cmp_maintenance_date') is-invalid @enderror"
                                >
                            </div>

                            <div class="mb-4">
                                <label for="cmp_completion_note" class="label-penapis">
                                    <i class="bi bi-pencil-square"></i>
                                    Catatan Penyelenggaraan
                                </label>

                                <textarea
                                    id="cmp_completion_note"
                                    name="cmp_completion_note"
                                    rows="5"
                                    class="form-control kawalan-penapis @error('cmp_completion_note') is-invalid @enderror"
                                    placeholder="Nyatakan tindakan penyelenggaraan yang telah dijalankan."
                                >{{ old('cmp_completion_note', $aduan->cmp_completion_note) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-tools me-2"></i>Simpan Kemaskini
                            </button>
                        </form>
                    </div>
                @else
                    <div class="panel-status-aduan">
                        <i class="bi bi-info-circle-fill"></i>

                        <div>
                            <strong>Status Semasa</strong>
                            <p class="mb-0">
                                @if ($ditolak)
                                    Aduan telah ditolak oleh BPH dan ditandakan sebagai selesai.
                                @elseif ($aduan->cmp_status === 'SELESAI')
                                    Aduan ini telah selesai diproses.
                                @elseif ($aduan->cmp_status === 'DITERIMA')
                                    Aduan telah diterima dan sedang menunggu tindakan KJ.
                                @else
                                    Aduan ini sedang menunggu keputusan BPH.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection