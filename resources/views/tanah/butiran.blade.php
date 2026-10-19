<div class="modal-header">
    <div>
        <p class="teks-kecil mb-1">BUTIRAN ASET TANAH</p>
        <h5 class="modal-title fw-bold">{{ $tanah->asl_ref_no }}</h5>
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
                <small class="text-secondary d-block mb-1">Lokasi Tanah</small>
                <strong>{{ $tanah->asl_location ?: '-' }}</strong>
            </div>
        </div>

        <div class="col-md-6">
            <small class="teks-kecil d-block">No. Lot</small>
            <span>{{ $tanah->asl_lot_no ?: '-' }}</span>
        </div>

        <div class="col-md-6">
            <small class="teks-kecil d-block">Keluasan Tanah</small>
            <span>
                {{ $tanah->asl_land_size !== null
                    ? number_format($tanah->asl_land_size, 2) . ' meter persegi'
                    : '-' }}
            </span>
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-md-6">
            <small class="teks-kecil d-block">No. Hakmilik</small>
            <span>{{ $tanah->asl_hakmilik_no ?: '-' }}</span>
        </div>

        <div class="col-md-6">
            <small class="teks-kecil d-block">Status Hakmilik</small>
            <span>{{ $tanah->asl_status_hakmilik ? str_replace('_', ' ', $tanah->asl_status_hakmilik) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Jenis Hakmilik</small>
            <span>{{ $tanah->asl_hakmilik_type ? str_replace('_', ' ', $tanah->asl_hakmilik_type) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Tarikh Terima Hakmilik</small>
            <span>{{ $tanah->asl_tarikh_terima_hakmilik?->format('d/m/Y') ?? '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Tarikh Serah Balik Hakmilik</small>
            <span>{{ $tanah->asl_tarikh_serah_balik_hakmilik?->format('d/m/Y') ?? '-' }}</span>
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Harga Tanah</small>
            <span>RM {{ $tanah->asl_land_price !== null ? number_format($tanah->asl_land_price, 2) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">SKP</small>
            <span>RM {{ $tanah->asl_land_skp !== null ? number_format($tanah->asl_land_skp, 2) : '-' }}</span>
        </div>

        <div class="col-md-4">
            <small class="teks-kecil d-block">Cukai Tanah</small>
            <span>RM {{ $tanah->asl_cukaiT !== null ? number_format($tanah->asl_cukaiT, 2) : '-' }}</span>
        </div>

        <div class="col-12">
            <small class="teks-kecil d-block">Status Cukai Tanah</small>
            <span>{{ $tanah->asl_status_cukaiT ?: '-' }}</span>
        </div>

        @if ($tanah->asl_gmaps_location)
            <div class="col-12">
                <a
                    href="{{ $tanah->asl_gmaps_location }}"
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

    <a href="{{ route('tanah.edit', $tanah) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i>Kemaskini
    </a>
</div>