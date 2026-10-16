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
                <label class="form-label">No. Kad Pengenalan / No. Syarikat</label>
                <input
                    type="text"
                    name="penyewa[{{ $indeks }}][tnt_ic_no]"
                    value="{{ old("penyewa.$indeks.tnt_ic_no", $penyewa?->tnt_ic_no) }}"
                    class="form-control"
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">No. Akaun Penyewa</label>
                <input
                    type="text"
                    name="penyewa[{{ $indeks }}][tnt_acc_no]"
                    value="{{ old("penyewa.$indeks.tnt_acc_no", $penyewa?->tnt_acc_no) }}"
                    class="form-control"
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Tarikh Mula Sewa</label>
                <input
                    type="date"
                    name="penyewa[{{ $indeks }}][tnt_start_date]"
                    value="{{ old("penyewa.$indeks.tnt_start_date", optional($penyewa?->tnt_start_date)->format('Y-m-d')) }}"
                    class="form-control"
                >
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Tarikh Tamat Sewa</label>
                <input
                    type="date"
                    name="penyewa[{{ $indeks }}][tnt_end_date]"
                    value="{{ old("penyewa.$indeks.tnt_end_date", optional($penyewa?->tnt_end_date)->format('Y-m-d')) }}"
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