<div class="modal-header">
    <div>
        <p class="teks-kecil mb-1">BUTIRAN ASET TANAH</p>
        <h5 class="modal-title fw-bold">{{ $tanah->asl_ref_no }}</h5>
        <small class="text-secondary">{{ $tanah->asl_lot_no ?: 'Tiada nombor lot' }}</small>
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="modal"
        aria-label="Tutup"
    ></button>
</div>

<div class="modal-body p-4">
    <div class="tab-butiran-melekat">
        <div class="tab-butiran" role="tablist" aria-label="Submenu butiran aset tanah">
            @foreach ([
                1 => 'Maklumat Tanah',
                2 => 'Hakmilik',
                3 => 'Lokasi Peta',
                4 => 'Nilai & Cukai',
                5 => 'Dokumen',
            ] as $nombor => $label)
                <button
                    type="button"
                    class="tab-butiran-butang"
                    data-tab-butiran-butang="{{ $nombor }}"
                    role="tab"
                    aria-selected="false"
                >
                    <span>{{ $nombor }}</span>
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <section class="panel-butiran aktif" data-tab-butiran-panel="1" role="tabpanel">
        <div class="butiran-seksyen">
            <h6>Maklumat Tanah</h6>
            <p>Butiran asas aset tanah yang telah direkodkan.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>No. Rujukan</dt>
                    <dd>{{ $tanah->asl_ref_no ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>No. Lot</dt>
                    <dd>{{ $tanah->asl_lot_no ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Keluasan Tanah</dt>
                    <dd>
                        {{ $tanah->asl_land_size !== null
                            ? number_format($tanah->asl_land_size, 2) . ' m²'
                            : '-' }}
                    </dd>
                </dl>
            </div>

            <div class="col-12">
                <dl class="butiran-medan">
                    <dt>Lokasi Tanah</dt>
                    <dd>{{ $tanah->asl_location ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="2" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Maklumat Hakmilik</h6>
            <p>Butiran status, jenis dan rekod penerimaan hakmilik tanah.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Nombor Hakmilik</dt>
                    <dd>{{ $tanah->asl_hakmilik_no ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Status Hakmilik</dt>
                    <dd>
                        {{ $tanah->asl_status_hakmilik
                            ? \App\Support\Paparan::label($tanah->asl_status_hakmilik)
                            : '-' }}
                    </dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Jenis Hakmilik</dt>
                    <dd>
                        {{ $tanah->asl_hakmilik_type
                            ? \App\Support\Paparan::label($tanah->asl_hakmilik_type)
                            : '-' }}
                    </dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Tarikh Terima Hakmilik</dt>
                    <dd>{{ $tanah->asl_tarikh_terima_hakmilik?->format('d/m/Y') ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Tarikh Serah Balik Hakmilik</dt>
                    <dd>{{ $tanah->asl_tarikh_serah_balik_hakmilik?->format('d/m/Y') ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="3" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Lokasi Peta</h6>
            <p>Pautan Google Maps dan koordinat kedudukan tanah.</p>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <dl class="butiran-medan">
                    <dt>Pautan Google Maps</dt>
                    <dd>
                        @if ($tanah->asl_gmaps_location)
                            <a href="{{ $tanah->asl_gmaps_location }}" target="_blank" rel="noopener">
                                <i class="bi bi-geo-alt-fill me-1"></i>Buka lokasi Google Maps
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Latitud</dt>
                    <dd>{{ $tanah->asl_latitude ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Longitud</dt>
                    <dd>{{ $tanah->asl_longitude ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="4" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Nilai & Cukai</h6>
            <p>Nilai kewangan serta status cukai tanah semasa.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Harga Tanah</dt>
                    <dd>RM {{ $tanah->asl_land_price !== null ? number_format($tanah->asl_land_price, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>SKP</dt>
                    <dd>RM {{ $tanah->asl_land_skp !== null ? number_format($tanah->asl_land_skp, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Cukai Tanah</dt>
                    <dd>RM {{ $tanah->asl_cukaiT !== null ? number_format($tanah->asl_cukaiT, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12">
                <dl class="butiran-medan">
                    <dt>Status Cukai Tanah</dt>
                    <dd>{{ $tanah->asl_status_cukaiT ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="5" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Dokumen Aset Tanah</h6>
            <p>{{ $tanah->dokumen->count() }} daripada maksimum 4 dokumen direkodkan.</p>
        </div>

        @forelse ($tanah->dokumen as $dokumen)
            <div class="butiran-medan mb-3">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                    <div class="ikon-dokumen">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <span class="lencana-dokumen">
                            {{ \App\Support\Paparan::label($dokumen->asldoc_type) }}
                        </span>

                        <strong class="d-block text-truncate mt-2">
                            {{ $dokumen->asldoc_name }}
                        </strong>

                        <small class="text-secondary">
                            Dimuat naik:
                            {{ $dokumen->uploaded_at?->format('d/m/Y, h:i A') ?? '-' }}
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <a
                            href="{{ asset('storage/' . $dokumen->asldoc_path) }}"
                            target="_blank"
                            rel="noopener"
                            class="btn btn-sm btn-outline-primary"
                        >
                            <i class="bi bi-eye me-1"></i>Lihat
                        </a>

                        <a
                            href="{{ asset('storage/' . $dokumen->asldoc_path) }}"
                            download="{{ $dokumen->asldoc_name }}"
                            class="btn btn-sm btn-outline-secondary"
                            title="Muat turun dokumen"
                        >
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-secondary">
                <i class="bi bi-folder2-open fs-3 d-block mb-2"></i>
                Tiada dokumen direkodkan untuk aset tanah ini.
            </div>
        @endforelse
    </section>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
        Tutup
    </button>

    <a href="{{ route('tanah.edit', $tanah) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i>Kemaskini
    </a>
</div>