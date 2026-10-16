@php
    $tanah = $tanah ?? null;
@endphp

<div class="kad-data">
    <form method="POST" action="{{ $action }}" data-borang-tanah novalidate>
        @csrf

        @if ($method === 'PUT')
            @method('PUT')
        @endif

        @if ($errors->has('tetapan'))
            <div class="p-4 pb-0">
                <p class="text-danger mb-0">{{ $errors->first('tetapan') }}</p>
            </div>
        @endif

        <div class="p-4 p-md-5 border-bottom">
            <div class="penunjuk-borang" data-penunjuk-tanah>
                <div class="penunjuk-item aktif" data-penunjuk-item="1">
                    <span>1</span>
                    <strong>Maklumat Tanah</strong>
                </div>

                <div class="penunjuk-item" data-penunjuk-item="2">
                    <span>2</span>
                    <strong>Hakmilik</strong>
                </div>

                <div class="penunjuk-item" data-penunjuk-item="3">
                    <span>3</span>
                    <strong>Lokasi Peta</strong>
                </div>

                <div class="penunjuk-item" data-penunjuk-item="4">
                    <span>4</span>
                    <strong>Nilai & Cukai</strong>
                </div>
            </div>

            <div class="garis-kemajuan">
                <span data-kemajuan-tanah></span>
            </div>
        </div>

        <div class="p-4 p-md-5 overflow-hidden">
            <section class="langkah-borang aktif" data-langkah="1">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Maklumat Tanah</h2>
                    <p class="text-secondary small mb-0">
                        Masukkan maklumat asas aset tanah.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label for="asl_lot_no" class="form-label fw-semibold">
                            Nombor Lot <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control @error('asl_lot_no') is-invalid @enderror"
                            id="asl_lot_no"
                            name="asl_lot_no"
                            value="{{ old('asl_lot_no', $tanah?->asl_lot_no) }}"
                            required
                        >

                        @error('asl_lot_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_land_size" class="form-label fw-semibold">
                            Keluasan Tanah <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                class="form-control @error('asl_land_size') is-invalid @enderror"
                                id="asl_land_size"
                                name="asl_land_size"
                                value="{{ old('asl_land_size', $tanah?->asl_land_size) }}"
                                required
                            >
                            <span class="input-group-text">m²</span>
                        </div>

                        @error('asl_land_size')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="asl_location" class="form-label fw-semibold">
                            Lokasi Tanah <span class="text-danger">*</span>
                        </label>

                        <textarea
                            class="form-control @error('asl_location') is-invalid @enderror"
                            id="asl_location"
                            name="asl_location"
                            rows="3"
                            required
                        >{{ old('asl_location', $tanah?->asl_location) }}</textarea>

                        @error('asl_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-5">
                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>

            <section class="langkah-borang" data-langkah="2">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Maklumat Hakmilik</h2>
                    <p class="text-secondary small mb-0">
                        Masukkan butiran hakmilik bagi tanah ini.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label for="asl_hakmilik_no" class="form-label fw-semibold">Nombor Hakmilik</label>
                        <input
                            type="text"
                            class="form-control"
                            id="asl_hakmilik_no"
                            name="asl_hakmilik_no"
                            value="{{ old('asl_hakmilik_no', $tanah?->asl_hakmilik_no) }}"
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_status_hakmilik" class="form-label fw-semibold">Status Hakmilik</label>
                        <select class="form-select" id="asl_status_hakmilik" name="asl_status_hakmilik">
                            <option value="">-- Pilih Status --</option>
                            @foreach (['HAKMILIK', 'RIZAB', 'STRATA', 'LAIN_LAIN'] as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected(old('asl_status_hakmilik', $tanah?->asl_status_hakmilik) === $status)
                                >
                                    {{ str_replace('_', ' ', $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="asl_hakmilik_type" class="form-label fw-semibold">Jenis Hakmilik</label>

                        <select class="form-select" id="asl_hakmilik_type" name="asl_hakmilik_type">
                            <option value="">-- Pilih Jenis --</option>

                            @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEKAL'] as $jenis)
                                <option
                                    value="{{ $jenis }}"
                                    @selected(old('asl_hakmilik_type', $tanah?->asl_hakmilik_type) === $jenis)
                                >
                                    {{ str_replace('_', ' ', $jenis) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="asl_tarikh_terima_hakmilik" class="form-label fw-semibold">
                            Tarikh Terima Hakmilik
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="asl_tarikh_terima_hakmilik"
                            name="asl_tarikh_terima_hakmilik"
                            value="{{ old('asl_tarikh_terima_hakmilik', $tanah?->asl_tarikh_terima_hakmilik?->format('Y-m-d')) }}"
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="asl_tarikh_serah_balik_hakmilik" class="form-label fw-semibold">
                            Tarikh Serah Balik Hakmilik
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="asl_tarikh_serah_balik_hakmilik"
                            name="asl_tarikh_serah_balik_hakmilik"
                            value="{{ old('asl_tarikh_serah_balik_hakmilik', $tanah?->asl_tarikh_serah_balik_hakmilik?->format('Y-m-d')) }}"
                        >
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelumnya>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>

                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>

            <section class="langkah-borang" data-langkah="3">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Lokasi Peta</h2>
                    <p class="text-secondary small mb-0">
                        Masukkan pautan Google Maps atau koordinat tanah jika tersedia.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <label for="asl_gmaps_location" class="form-label fw-semibold">
                            Pautan Google Maps
                        </label>
                        <input
                            type="url"
                            class="form-control @error('asl_gmaps_location') is-invalid @enderror"
                            id="asl_gmaps_location"
                            name="asl_gmaps_location"
                            value="{{ old('asl_gmaps_location', $tanah?->asl_gmaps_location) }}"
                            placeholder="https://maps.google.com/..."
                        >
                        @error('asl_gmaps_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_latitude" class="form-label fw-semibold">Latitud</label>
                        <input
                            type="number"
                            step="0.0000001"
                            class="form-control @error('asl_latitude') is-invalid @enderror"
                            id="asl_latitude"
                            name="asl_latitude"
                            value="{{ old('asl_latitude', $tanah?->asl_latitude) }}"
                            placeholder="Contoh: 5.206625"
                        >
                        @error('asl_latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_longitude" class="form-label fw-semibold">Longitud</label>
                        <input
                            type="number"
                            step="0.0000001"
                            class="form-control @error('asl_longitude') is-invalid @enderror"
                            id="asl_longitude"
                            name="asl_longitude"
                            value="{{ old('asl_longitude', $tanah?->asl_longitude) }}"
                            placeholder="Contoh: 103.142013"
                        >
                        @error('asl_longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelumnya>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>

                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>

            <section class="langkah-borang" data-langkah="4">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Nilai & Cukai</h2>
                    <p class="text-secondary small mb-0">
                        Masukkan nilai kewangan dan maklumat cukai tanah jika tersedia.
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label for="asl_land_price" class="form-label fw-semibold">Harga Tanah (RM)</label>
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            id="asl_land_price"
                            name="asl_land_price"
                            value="{{ old('asl_land_price', $tanah?->asl_land_price) }}"
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_land_skp" class="form-label fw-semibold">SKP (RM)</label>
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            id="asl_land_skp"
                            name="asl_land_skp"
                            value="{{ old('asl_land_skp', $tanah?->asl_land_skp) }}"
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_cukaiT" class="form-label fw-semibold">Cukai Tanah (RM)</label>
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            id="asl_cukaiT"
                            name="asl_cukaiT"
                            value="{{ old('asl_cukaiT', $tanah?->asl_cukaiT) }}"
                        >
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asl_status_cukaiT" class="form-label fw-semibold">
                            Status Cukai Tanah
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="asl_status_cukaiT"
                            name="asl_status_cukaiT"
                            value="{{ old('asl_status_cukaiT', $tanah?->asl_status_cukaiT) }}"
                            placeholder="Contoh: Telah Dibayar"
                        >
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelumnya>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>

                    <button type="submit" class="btn btn-primary px-4" data-simpan-tanah>
                        <i class="bi bi-check-lg me-2"></i>{{ $butang }}
                    </button>
                </div>
            </section>
        </div>
    </form>
</div>