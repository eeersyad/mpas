@php
    $tanah = $tanah ?? null;
@endphp

<div class="kad-data borang-aset">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" data-borang-tanah novalidate>
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
                <div class="penunjuk-item" data-penunjuk-item="5">
                    <span>5</span>
                    <strong>Dokumen</strong>
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
                    <div class="col-12 col-md-4">
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

                    <div class="col-12 col-md-4">
                        <label for="asl_no_file" class="form-label fw-semibold">
                            No. Fail
                        </label>

                        <input
                            type="text"
                            id="asl_no_file"
                            name="asl_no_file"
                            class="form-control @error('asl_no_file') is-invalid @enderror"
                            value="{{ old('asl_no_file', $tanah?->asl_no_file) }}"
                            placeholder="Contoh: MDM/TNH/01/2026"
                        >

                        @error('asl_no_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
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
                        <label for="asl_status_tanah" class="form-label fw-semibold">Status Tanah</label>

                        <select class="form-select" id="asl_status_tanah" name="asl_status_tanah">
                            <option value="">-- Pilih Status Tanah --</option>

                            @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK'] as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected(old('asl_status_tanah', $tanah?->asl_status_tanah) === $status)
                                >
                                    {{ \App\Support\Paparan::label($status) }}
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

                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>
            <section class="langkah-borang" data-langkah="5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Dokumen Aset Tanah</h2>
                        <p class="text-secondary small mb-0">
                            Muat naik dokumen seperti geran, pelan lokasi, resit cukai atau gambar tanah.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary px-4"
                        data-tambah-dokumen-tanah
                    >
                        <i class="bi bi-file-earmark-plus me-1"></i>Tambah Dokumen
                    </button>
                </div>

                <div class="alert alert-light border small mb-4">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    Dokumen adalah pilihan. Maksimum <strong>4 dokumen</strong> bagi satu aset tanah.
                    Format dibenarkan: PDF, JPG, JPEG, PNG dan WEBP. Saiz maksimum setiap fail ialah 10MB.
                </div>

                @error('dokumen')
                    <div class="alert alert-danger small">{{ $message }}</div>
                @enderror

                <div
                    class="row g-3"
                    data-senarai-dokumen-tanah
                    data-indeks-seterusnya="0"
                    data-dokumen-sedia-ada="{{ $tanah?->dokumen->count() ?? 0 }}"
                >
                    <div class="col-12" data-mesej-dokumen-tanah>
                        <div class="kad-kosong py-4">
                            <div class="ilustrasi-kosong">
                                <i class="bi bi-folder2-open"></i>
                            </div>

                            <h3>Tiada dokumen baharu</h3>
                            <p>Tekan “Tambah Dokumen” jika ada dokumen untuk dimuat naik.</p>
                        </div>
                    </div>
                </div>

                @if ($tanah && $tanah->dokumen->isNotEmpty())
                    <div class="mt-4 pt-4 border-top">
                        <h3 class="subtajuk-seksyen mb-3">
                            Dokumen Sedia Ada ({{ $tanah->dokumen->count() }}/4)
                        </h3>

                        <div class="row g-3">
                            @foreach ($tanah->dokumen as $dokumen)
                                <div class="col-12 col-md-6">
                                    <div class="kad-dokumen">
                                        <div class="d-flex gap-3 align-items-start">
                                            <div class="ikon-dokumen">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </div>

                                            <div class="flex-grow-1 min-w-0">
                                                <span class="lencana-dokumen">
                                                    {{ str_replace('_', ' ', $dokumen->asldoc_type) }}
                                                </span>

                                                <p class="fw-semibold text-truncate mb-1 mt-2">
                                                    {{ $dokumen->asldoc_name }}
                                                </p>

                                                <small class="text-secondary d-block">
                                                    Dimuat naik:
                                                    {{ $dokumen->uploaded_at?->format('d/m/Y, h:i A') ?? '-' }}
                                                </small>

                                                <div class="d-flex gap-2 mt-3">
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
                                                    >
                                                        <i class="bi bi-download"></i>
                                                    </a>

                                                    <button
                                                        type="submit"
                                                        form="formPadamDokumenTanah{{ $dokumen->asldoc_id }}"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Padam dokumen"
                                                    >
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <template id="templatDokumenTanah">
                    <div class="col-12" data-dokumen-tanah>
                        <div class="kad-dokumen-baharu">
                            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                                <h3 class="subtajuk-seksyen mb-0">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Dokumen Baharu
                                </h3>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    data-buang-dokumen-tanah
                                >
                                    <i class="bi bi-trash3 me-1"></i>Buang
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">
                                        Jenis Dokumen <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="dokumen[__INDEX__][jenis]"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="GERAN">Geran / Hak Milik</option>
                                        <option value="PELAN_LOKASI">Pelan Lokasi</option>
                                        <option value="SURAT_KELULUSAN">Surat Kelulusan</option>
                                        <option value="RESIT_CUKAI">Resit Cukai Tanah</option>
                                        <option value="GAMBAR">Gambar Tanah</option>
                                        <option value="LAIN_LAIN">Lain-lain</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-8">
                                    <label class="form-label">
                                        Fail Dokumen <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="file"
                                        name="dokumen[__INDEX__][fail]"
                                        class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png,.webp"
                                        required
                                    >

                                    <div class="form-text">
                                        PDF, JPG, JPEG, PNG atau WEBP. Maksimum 10MB.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelumnya>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-2"></i>{{ $butang }}
                    </button>
                </div>
            </section>
        </div>
    </form>
    @if ($tanah)
        @foreach ($tanah->dokumen as $dokumen)
            <form
                id="formPadamDokumenTanah{{ $dokumen->asldoc_id }}"
                method="POST"
                action="{{ route('tanah.dokumen.hapus', [$tanah, $dokumen]) }}"
                data-padam-dokumen-tanah
                data-nama-dokumen="{{ $dokumen->asldoc_name }}"
            >
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif
</div>