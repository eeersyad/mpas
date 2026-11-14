{{-- Bottom sheet untuk ubah satu medan (telefon). Diisi oleh JS daripada [data-sunting-medan] --}}
<div class="offcanvas offcanvas-bottom panel-penapis-mobile d-md-none" tabindex="-1" id="lembaranSuntingMedan" aria-labelledby="lembaranSuntingMedanTajuk">
    <div class="pemegang-panel" aria-hidden="true"></div>

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="lembaranSuntingMedanTajuk" data-tajuk-sunting>Ubah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>

    <div class="offcanvas-body pt-0">
        <input type="text" class="form-control medan-sunting-apl" data-input-sunting aria-labelledby="lembaranSuntingMedanTajuk">

        <div class="pratonton-rujukan mt-3 d-none" data-pratonton-sunting>
            <span><i class="bi bi-eye me-1"></i>Pratonton</span>
            <strong>-</strong>
        </div>

        <button type="button" class="btn btn-utama w-100 mt-3" data-simpan-sunting>Selesai</button>
    </div>
</div>
