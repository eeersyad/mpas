<div class="modal-header">
    <div>
        <p class="teks-kecil mb-1">BUTIRAN ASET BANGUNAN</p>
        <h5 class="modal-title fw-bold">{{ $bangunan->asb_name }}</h5>
        <small class="text-secondary">{{ $bangunan->asb_ref_no }}</small>
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="modal"
        aria-label="Tutup"
    ></button>
</div>

<div class="modal-body p-4">
    <div class="row g-3">
        <div class="col-12">
            <div class="p-3 rounded-3 border bg-light">
                <small class="text-secondary d-block mb-1">Alamat</small>
                <strong>{{ $bangunan->asb_add1 ?: '-' }}</strong>

                @if ($bangunan->asb_add2)
                    <div>{{ $bangunan->asb_add2 }}</div>
                @endif

                <div>
                    {{ collect([
                        $bangunan->asb_postcode,
                        $bangunan->asb_city,
                        $bangunan->asb_state,
                    ])->filter()->implode(', ') }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Kategori</small>
            <span>{{ $bangunan->asb_category ? str_replace('_', ' ', $bangunan->asb_category) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Status Premis</small>
            <span>{{ str_replace('_', ' ', $bangunan->asb_premise_status) }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Status Penyelenggaraan</small>
            <span>{{ str_replace('_', ' ', $bangunan->asb_maintenance_status) }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Keluasan</small>
            <span>
                {{ $bangunan->asb_size !== null
                    ? number_format($bangunan->asb_size, 2) . ' meter persegi'
                    : '-' }}
            </span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Jumlah Blok</small>
            <span>{{ $bangunan->asb_total_block }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Jumlah Struktur Luar</small>
            <span>{{ $bangunan->asb_total_ext_structure }}</span>
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Kos Siap Bina Asal</small>
            <span>RM {{ $bangunan->asb_kos_siap_bina_asal !== null ? number_format($bangunan->asb_kos_siap_bina_asal, 2) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Kos Tambahan</small>
            <span>RM {{ $bangunan->asb_kos_tambahan !== null ? number_format($bangunan->asb_kos_tambahan, 2) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Jumlah Kos</small>
            <span>RM {{ $bangunan->asb_total_kos !== null ? number_format($bangunan->asb_total_kos, 2) : '-' }}</span>
        </div>

        <div class="col-12">
            <small class="teks-kecil d-block">Nilai Semasa</small>
            <span>RM {{ $bangunan->asb_current_value !== null ? number_format($bangunan->asb_current_value, 2) : '-' }}</span>
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                <div>
                    <small class="teks-kecil d-block">Penyewa Aktif</small>
                    <strong>{{ $bangunan->penyewa->where('tnt_status', 'AKTIF')->count() }} penyewa</strong>
                </div>
            </div>

            @forelse ($bangunan->penyewa->where('tnt_status', 'AKTIF') as $penyewa)
                <div class="border rounded-3 p-3 mb-2">
                    <strong>{{ $penyewa->tnt_name }}</strong>
                    <div class="small text-secondary">
                        Tamat sewa:
                        {{ $penyewa->tnt_end_date?->format('d/m/Y') ?? '-' }}
                    </div>
                </div>
            @empty
                <p class="text-secondary mb-0">Tiada penyewa aktif direkodkan.</p>
            @endforelse
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-12">
            <small class="teks-kecil d-block mb-2">Dokumen Aset</small>

            @forelse ($bangunan->dokumen as $dokumen)
                <a
                    href="{{ asset('storage/' . $dokumen->asbdoc_path) }}"
                    target="_blank"
                    rel="noopener"
                    class="d-flex align-items-center gap-3 border rounded-3 p-3 mb-2 text-decoration-none"
                >
                    <div class="ikon-dokumen">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                    <div>
                        <strong class="d-block text-dark">{{ $dokumen->asbdoc_name }}</strong>
                        <small class="text-secondary">
                            {{ str_replace('_', ' ', $dokumen->asbdoc_type) }}
                            ·
                            {{ $dokumen->uploaded_at?->format('d/m/Y') ?? '-' }}
                        </small>
                    </div>

                    <i class="bi bi-box-arrow-up-right ms-auto text-primary"></i>
                </a>
            @empty
                <p class="text-secondary mb-0">Tiada dokumen direkodkan.</p>
            @endforelse
        </div>

        <div class="col-12"><hr class="my-1"></div>

        @if ($bangunan->kod)
            @php
                $pautanAduan = rtrim((string) config('app.url'), '/')
                    . '/laporan/'
                    . $bangunan->kod->qr_code_value;
            @endphp

            <div class="col-12">
                <div class="kad-qr-butiran">
                    <div class="qr-butiran-imej">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                            ->size(180)
                            ->margin(1)
                            ->generate($pautanAduan) !!}
                    </div>

                    <div class="qr-butiran-kandungan">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                            <div>
                                <span class="qr-butiran-label">
                                    <i class="bi bi-qr-code-scan"></i>
                                    KOD QR ADUAN
                                </span>

                                <h6 class="mt-2 mb-1 fw-bold">Aduan Aset Bangunan</h6>

                                <p class="text-secondary small mb-0">
                                    Imbas kod QR ini untuk membuat aduan berkaitan
                                    <strong>{{ $bangunan->asb_name }}</strong>.
                                </p>
                            </div>

                            <span class="badge {{ $bangunan->kod->qr_status === 'ACTIVE' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                <i class="bi {{ $bangunan->kod->qr_status === 'ACTIVE' ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }} me-1"></i>
                                {{ $bangunan->kod->qr_status === 'ACTIVE' ? 'QR Aktif' : 'QR Tidak Aktif' }}
                            </span>
                        </div>

                        <div class="qr-pautan mt-3">
                            <i class="bi bi-link-45deg"></i>

                            <span class="text-truncate">{{ $pautanAduan }}</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a
                                href="{{ route('kodqr.show', $bangunan) }}"
                                class="btn btn-primary btn-sm"
                            >
                                <i class="bi bi-qr-code me-1"></i>Urus Kod QR
                            </a>

                            <a
                                href="{{ $pautanAduan }}"
                                target="_blank"
                                rel="noopener"
                                class="btn btn-outline-primary btn-sm"
                            >
                                <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Pautan Aduan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Kod QR belum dijana untuk aset ini.
                </div>
            </div>
        @endif

        <div class="col-12">
            <small class="teks-kecil d-block">Rekod Aduan Terkini</small>
            <strong>{{ $bangunan->aduan->count() }} aduan dipaparkan</strong>
        </div>

        @if ($bangunan->asb_gmaps_location)
            <div class="col-12">
                <a
                    href="{{ $bangunan->asb_gmaps_location }}"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-outline-primary btn-sm"
                >
                    <i class="bi bi-geo-alt me-1"></i>Buka lokasi Google Maps
                </a>
            </div>
        @endif
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
        Tutup
    </button>

    <a href="{{ route('bangunan.edit', $bangunan) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i>Kemaskini
    </a>
</div>