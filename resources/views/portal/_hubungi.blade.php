<section class="kad-awam portal-hubungi">
    <div class="d-flex align-items-center gap-3 mb-3">
        <span class="ikon-borang-awam"><i class="bi bi-telephone"></i></span>
        <div>
            <h2 class="h6 fw-bold mb-0">Berminat untuk menyewa?</h2>
            <small class="text-muted">Hubungi {{ $hubungan['nama'] }} untuk maklumat lanjut.</small>
        </div>
    </div>
    <div class="d-grid gap-2 d-sm-flex">
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hubungan['telefon']) }}" class="btn btn-tema flex-fill"><i class="bi bi-telephone-fill me-1"></i> {{ $hubungan['telefon'] }}</a>
        <a href="mailto:{{ $hubungan['emel'] }}" class="btn btn-outline-tema flex-fill"><i class="bi bi-envelope me-1"></i> {{ $hubungan['emel'] }}</a>
    </div>
</section>
