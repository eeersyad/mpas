@php
    $penyewa = $penyewa ?? null;
@endphp

<div class="col-12" data-penyewa>
    <div class="kad-penyewa">
        <input
            type="hidden"
            name="penyewa[{{ $indeks }}][tnt_id]"
            value="{{ $penyewa?->tnt_id }}"
        >

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0 subtajuk-seksyen">
                <i class="bi bi-person-vcard me-2"></i>Penyewa
            </h3>

            <button type="button" class="btn btn-sm btn-outline-danger" data-buang-penyewa>
                <i class="bi bi-trash3 me-1"></i> Buang
            </button>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Nama Penyewa <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="penyewa[{{ $indeks }}][tnt_name]"
                    value="{{ old("penyewa.$indeks.tnt_name", $penyewa?->tnt_name) }}"
                    class="form-control"
                    required
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">
                    No. Kad Pengenalan / No. Syarikat <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="penyewa[{{ $indeks }}][tnt_ic_no]"
                    value="{{ old("penyewa.$indeks.tnt_ic_no", $penyewa?->tnt_ic_no) }}"
                    class="form-control"
                    required
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">
                    No. Akaun Penyewa <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="penyewa[{{ $indeks }}][tnt_acc_no]"
                    value="{{ old("penyewa.$indeks.tnt_acc_no", $penyewa?->tnt_acc_no) }}"
                    class="form-control"
                    required
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Tarikh Mula Perjanjian</label>
                <input
                    type="date"
                    name="penyewa[{{ $indeks }}][tnt_mula_perjanjian]"
                    value="{{ old("penyewa.$indeks.tnt_mula_perjanjian", optional($penyewa?->tnt_mula_perjanjian)->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Tarikh Tamat Perjanjian</label>
                <input
                    type="date"
                    name="penyewa[{{ $indeks }}][tnt_tamat_perjanjian]"
                    value="{{ old("penyewa.$indeks.tnt_tamat_perjanjian", optional($penyewa?->tnt_tamat_perjanjian)->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Status Penyewa</label>
                <select name="penyewa[{{ $indeks }}][tnt_status]" class="form-select">
                    <option value="AKTIF" @selected(old("penyewa.$indeks.tnt_status", $penyewa?->tnt_status) === 'AKTIF')>
                        Aktif
                    </option>
                    <option value="TIDAK_AKTIF" @selected(old("penyewa.$indeks.tnt_status", $penyewa?->tnt_status) === 'TIDAK_AKTIF')>
                        Tidak Aktif
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>