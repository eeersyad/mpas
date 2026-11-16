@php
    $telefon = preg_replace('/[^0-9+]/', '', $hubungan['telefon']);
@endphp

{{-- Desktop: kad hubungan --}}
<section class="kad-awam portal-hubungi d-none d-md-block">
    <div class="d-flex align-items-center gap-3 mb-3">
        <span class="ikon-borang-awam"><i class="bi bi-telephone"></i></span>
        <div>
            <h2 class="h6 fw-bold mb-0">Berminat untuk menyewa?</h2>
            <small class="text-muted">Hubungi {{ $hubungan['nama'] }} melalui telefon atau emel untuk urusan sewaan.</small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="tel:{{ $telefon }}" class="btn btn-tema flex-fill"><i class="bi bi-telephone-fill me-1"></i> {{ $hubungan['telefon'] }}</a>
        <a href="mailto:{{ $hubungan['emel'] }}" class="btn btn-outline-tema flex-fill"><i class="bi bi-envelope me-1"></i> {{ $hubungan['emel'] }}</a>
    </div>
</section>

{{-- Mobile: bottom sheet hubungan --}}
<div class="offcanvas offcanvas-bottom panel-penapis-mobile" tabindex="-1" id="lembaranHubungi" aria-labelledby="lembaranHubungiTajuk">
    <div class="pemegang-panel" aria-hidden="true"></div>
    <div class="offcanvas-header">
        <div>
            <h5 class="offcanvas-title mb-0" id="lembaranHubungiTajuk">Hubungi Kami</h5>
            <small class="text-muted">{{ $hubungan['nama'] }}</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body pb-4">
        <a href="tel:{{ $telefon }}" class="portal-baris-hubungi">
            <span class="portal-baris-ikon"><i class="bi bi-telephone-fill"></i></span>
            <span class="flex-fill"><small>Telefon</small><strong>{{ $hubungan['telefon'] }}</strong></span>
            <i class="bi bi-chevron-right"></i>
        </a>
        <a href="mailto:{{ $hubungan['emel'] }}" class="portal-baris-hubungi">
            <span class="portal-baris-ikon"><i class="bi bi-envelope-fill"></i></span>
            <span class="flex-fill text-truncate"><small>Emel</small><strong>{{ $hubungan['emel'] }}</strong></span>
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>
</div>
