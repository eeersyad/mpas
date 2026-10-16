@php
    $bangunan = $bangunan ?? null;
    $penyewaAktif = $bangunan?->penyewa?->where('tnt_status', 'AKTIF')->values() ?? collect();
@endphp

<div class="kad-data">
    <form method="POST" action="{{ $action }}" data-borang-bangunan>
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger mx-4 mx-md-5 mt-4 mb-0">
                <strong class="d-block mb-2">Pendaftaran tidak dapat disimpan:</strong>

                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $ralat)
                        <li>{{ $ralat }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($method === 'PUT')
            @method('PUT')
        @endif

        @if ($errors->has('tetapan'))
            <div class="p-4 pb-0 text-danger">
                {{ $errors->first('tetapan') }}
            </div>
        @endif

        <div class="p-4 p-md-5 border-bottom">
            <div class="penunjuk-borang">
                @foreach ([
                    'Maklumat Asas',
                    'Lokasi & Agensi',
                    'Maklumat Fizikal',
                    'Kos & Penilaian',
                    'Status & Pengesahan',
                    'Penyewa',
                ] as $nombor => $label)
                    <div
                        class="penunjuk-item {{ $nombor === 0 ? 'aktif' : '' }}"
                        data-penunjuk-bangunan="{{ $nombor + 1 }}"
                    >
                        <span>{{ $nombor + 1 }}</span>
                        <strong>{{ $label }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="garis-kemajuan">
                <span data-kemajuan-bangunan></span>
            </div>
        </div>

        <div class="p-4 p-md-5 overflow-hidden">
            <section class="langkah-borang aktif" data-langkah="1">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Maklumat Asas</h2>
                    <p class="text-secondary small mb-0">Masukkan butiran asas bangunan.</p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Nama Bangunan <span class="text-danger">*</span></label>
                        <input type="text" name="asb_name" class="form-control @error('asb_name') is-invalid @enderror"
                            value="{{ old('asb_name', $bangunan?->asb_name) }}" required>
                        @error('asb_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="asb_category" class="form-select @error('asb_category') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach (['PREMIS_PERNIAGAAN', 'TANDAS', 'DEWAN', 'PONDOK_BAS'] as $kategori)
                                <option value="{{ $kategori }}" @selected(old('asb_category', $bangunan?->asb_category) === $kategori)>
                                    {{ str_replace('_', ' ', $kategori) }}
                                </option>
                            @endforeach
                        </select>
                        @error('asb_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Subkategori</label>
                        <input type="text" name="asb_sub_category" class="form-control"
                            value="{{ old('asb_sub_category', $bangunan?->asb_sub_category) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Poskod</label>
                        <input type="text" name="asb_postcode" class="form-control"
                            value="{{ old('asb_postcode', $bangunan?->asb_postcode) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Baris 1 <span class="text-danger">*</span></label>
                        <input type="text" name="asb_add1" class="form-control @error('asb_add1') is-invalid @enderror"
                            value="{{ old('asb_add1', $bangunan?->asb_add1) }}" required>
                        @error('asb_add1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Baris 2</label>
                        <input type="text" name="asb_add2" class="form-control"
                            value="{{ old('asb_add2', $bangunan?->asb_add2) }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Daerah</label>
                        <input type="text" name="asb_distinct" class="form-control"
                            value="{{ old('asb_distinct', $bangunan?->asb_distinct) }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Mukim</label>
                        <input type="text" name="asb_mukim" class="form-control"
                            value="{{ old('asb_mukim', $bangunan?->asb_mukim) }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Bandar</label>
                        <input type="text" name="asb_city" class="form-control"
                            value="{{ old('asb_city', $bangunan?->asb_city) }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Negeri</label>
                        <input type="text" name="asb_state" class="form-control"
                            value="{{ old('asb_state', $bangunan?->asb_state) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Negara</label>
                        <input type="text" name="asb_country" class="form-control"
                            value="{{ old('asb_country', $bangunan?->asb_country ?? 'Malaysia') }}">
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
                    <h2 class="h5 fw-bold mb-1">Lokasi & Agensi</h2>
                    <p class="text-secondary small mb-0">Masukkan maklumat lokasi dan agensi berkaitan.</p>
                </div>

                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Pautan Google Maps</label>
                        <input type="url" name="asb_gmaps_location"
                            class="form-control @error('asb_gmaps_location') is-invalid @enderror"
                            value="{{ old('asb_gmaps_location', $bangunan?->asb_gmaps_location) }}"
                            placeholder="https://maps.google.com/...">
                        @error('asb_gmaps_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Latitud</label>
                        <input type="number" step="0.0000001" name="asb_latitude" class="form-control"
                            value="{{ old('asb_latitude', $bangunan?->asb_latitude) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Longitud</label>
                        <input type="number" step="0.0000001" name="asb_longitude" class="form-control"
                            value="{{ old('asb_longitude', $bangunan?->asb_longitude) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Agensi</label>
                        <input type="text" name="asb_agency" class="form-control"
                            value="{{ old('asb_agency', $bangunan?->asb_agency) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Kementerian</label>
                        <input type="text" name="asb_ministry" class="form-control"
                            value="{{ old('asb_ministry', $bangunan?->asb_ministry) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Jabatan</label>
                        <input type="text" name="asb_dept" class="form-control"
                            value="{{ old('asb_dept', $bangunan?->asb_dept) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Kod PTJ</label>
                        <input type="text" name="asb_ptj_code" class="form-control"
                            value="{{ old('asb_ptj_code', $bangunan?->asb_ptj_code) }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelum>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>

            <section class="langkah-borang" data-langkah="3">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Maklumat Fizikal</h2>
                    <p class="text-secondary small mb-0">Masukkan ciri fizikal dan akaun utiliti bangunan.</p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Keluasan Bangunan (m²)</label>
                        <input type="number" min="0" step="0.01" name="asb_size" class="form-control"
                            value="{{ old('asb_size', $bangunan?->asb_size) }}">
                    </div>

                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="asb_aset_warisan" name="asb_aset_warisan" value="1"
                                @checked(old('asb_aset_warisan', $bangunan?->asb_aset_warisan))>
                            <label class="form-check-label fw-semibold" for="asb_aset_warisan">
                                Dikategorikan sebagai aset warisan
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Jumlah Blok</label>
                        <input type="number" min="0" name="asb_total_block" class="form-control"
                            value="{{ old('asb_total_block', $bangunan?->asb_total_block ?? 0) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Jumlah Struktur Luaran</label>
                        <input type="number" min="0" name="asb_total_ext_structure" class="form-control"
                            value="{{ old('asb_total_ext_structure', $bangunan?->asb_total_ext_structure ?? 0) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">No. Akaun TNB</label>
                        <input type="text" name="asb_tnb_acc_no" class="form-control"
                            value="{{ old('asb_tnb_acc_no', $bangunan?->asb_tnb_acc_no) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">No. Akaun SATU</label>
                        <input type="text" name="asb_satu_acc_no" class="form-control"
                            value="{{ old('asb_satu_acc_no', $bangunan?->asb_satu_acc_no) }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelum>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>

            <section class="langkah-borang" data-langkah="4">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Kos & Penilaian</h2>
                    <p class="text-secondary small mb-0">Jumlah kos akan dikira oleh sistem secara automatik.</p>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Kos Siap Bina Asal (RM)</label>
                        <input type="number" min="0" step="0.01" name="asb_kos_siap_bina_asal" class="form-control"
                            value="{{ old('asb_kos_siap_bina_asal', $bangunan?->asb_kos_siap_bina_asal) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Kos Tambahan (RM)</label>
                        <input type="number" min="0" step="0.01" name="asb_kos_tambahan" class="form-control"
                            value="{{ old('asb_kos_tambahan', $bangunan?->asb_kos_tambahan) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tarikh Siap Bina</label>
                        <input type="date" name="asb_completion_date" class="form-control"
                            value="{{ old('asb_completion_date', $bangunan?->asb_completion_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Sumber Pembiayaan</label>
                        <input type="text" name="asb_financing_source" class="form-control"
                            value="{{ old('asb_financing_source', $bangunan?->asb_financing_source) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Nilai Semasa (RM)</label>
                        <input type="number" min="0" step="0.01" name="asb_current_value" class="form-control"
                            value="{{ old('asb_current_value', $bangunan?->asb_current_value) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tarikh Penilaian</label>
                        <input type="date" name="asb_assessment_date" class="form-control"
                            value="{{ old('asb_assessment_date', $bangunan?->asb_assessment_date?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-light px-4" data-sebelum>
                        <i class="bi bi-arrow-left me-2"></i>Sebelumnya
                    </button>
                    <button type="button" class="btn btn-primary px-4" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </section>

            {{-- Langkah 5: Status dan pengesahan --}}
            <section class="langkah-borang" data-langkah="5">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Status dan Pengesahan</h2>
                    <p class="text-secondary small mb-0">
                        Rekodkan status semasa aset serta maklumat pegawai yang membuat semakan.
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="asb_maintenance_status" class="form-label">
                            Status Penyelenggaraan
                        </label>
                        <select
                            id="asb_maintenance_status"
                            name="asb_maintenance_status"
                            class="form-select @error('asb_maintenance_status') is-invalid @enderror"
                        >
                            <option value="">-- Pilih Status --</option>
                            <option value="RENDAH" @selected(old('asb_maintenance_status', $bangunan?->asb_maintenance_status) === 'RENDAH')>
                                Rendah
                            </option>
                            <option value="SEDERHANA" @selected(old('asb_maintenance_status', $bangunan?->asb_maintenance_status) === 'SEDERHANA')>
                                Sederhana
                            </option>
                            <option value="KRITIKAL" @selected(old('asb_maintenance_status', $bangunan?->asb_maintenance_status) === 'KRITIKAL')>
                                Kritikal
                            </option>
                        </select>

                        @error('asb_maintenance_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="asb_premise_status" class="form-label">
                            Status Premis
                        </label>
                        <select
                            id="asb_premise_status"
                            name="asb_premise_status"
                            class="form-select @error('asb_premise_status') is-invalid @enderror"
                        >
                            <option value="">-- Pilih Status --</option>

                            <option
                                value="SEWA"
                                @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === 'SEWA')
                            >
                                Sewa
                            </option>

                            <option
                                value="TIDAK_DISEWA"
                                @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === 'TIDAK_DISEWA')
                            >
                                Tidak Disewa
                            </option>

                            <option
                                value="PAJAK"
                                @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === 'PAJAK')
                            >
                                Pajak
                            </option>

                            <option
                                value="TIDAK_DIPAJAK"
                                @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === 'TIDAK_DIPAJAK')
                            >
                                Tidak Dipajak
                            </option>

                            <option
                                value="KEGUNAAN_MDM"
                                @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === 'KEGUNAAN_MDM')
                            >
                                Kegunaan MDM
                            </option>

                            <option
                                value="DILUPUS"
                                @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === 'DILUPUS')
                            >
                                Dilupus
                            </option>
                        </select>

                        @error('asb_premise_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <h3 class="subtajuk-seksyen">Maklumat Pengumpulan</h3>
                    </div>

                    <div class="col-12 col-md-5">
                        <label for="asb_collector_name" class="form-label">
                            Nama Pegawai Pengumpul
                        </label>
                        <input
                            type="text"
                            id="asb_collector_name"
                            name="asb_collector_name"
                            value="{{ old('asb_collector_name', $bangunan?->asb_collector_name) }}"
                            class="form-control @error('asb_collector_name') is-invalid @enderror"
                        >

                        @error('asb_collector_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="asb_collector_position" class="form-label">
                            Jawatan Pegawai Pengumpul
                        </label>
                        <input
                            type="text"
                            id="asb_collector_position"
                            name="asb_collector_position"
                            value="{{ old('asb_collector_position', $bangunan?->asb_collector_position) }}"
                            class="form-control @error('asb_collector_position') is-invalid @enderror"
                        >

                        @error('asb_collector_position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="asb_collected_at" class="form-label">
                            Tarikh Dikumpul
                        </label>
                        <input
                            type="date"
                            id="asb_collected_at"
                            name="asb_collected_at"
                            value="{{ old('asb_collected_at', optional($bangunan?->asb_collected_at)->format('Y-m-d')) }}"
                            class="form-control @error('asb_collected_at') is-invalid @enderror"
                        >

                        @error('asb_collected_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <h3 class="subtajuk-seksyen">Maklumat Pengesahan</h3>
                    </div>

                    <div class="col-12 col-md-5">
                        <label for="asb_verifier_name" class="form-label">
                            Nama Pegawai Pengesah
                        </label>
                        <input
                            type="text"
                            id="asb_verifier_name"
                            name="asb_verifier_name"
                            value="{{ old('asb_verifier_name', $bangunan?->asb_verifier_name) }}"
                            class="form-control @error('asb_verifier_name') is-invalid @enderror"
                        >

                        @error('asb_verifier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="asb_verifier_position" class="form-label">
                            Jawatan Pegawai Pengesah
                        </label>
                        <input
                            type="text"
                            id="asb_verifier_position"
                            name="asb_verifier_position"
                            value="{{ old('asb_verifier_position', $bangunan?->asb_verifier_position) }}"
                            class="form-control @error('asb_verifier_position') is-invalid @enderror"
                        >

                        @error('asb_verifier_position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="asb_verified_at" class="form-label">
                            Tarikh Disahkan
                        </label>
                        <input
                            type="date"
                            id="asb_verified_at"
                            name="asb_verified_at"
                            value="{{ old('asb_verified_at', optional($bangunan?->asb_verified_at)->format('Y-m-d')) }}"
                            class="form-control @error('asb_verified_at') is-invalid @enderror"
                        >

                        @error('asb_verified_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3">
                    <button type="button" class="btn btn-outline-secondary" data-sebelum>
                        <i class="bi bi-arrow-left me-1"></i> Sebelum
                    </button>

                    <button type="button" class="btn btn-utama" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </section>

            {{-- Langkah 6: Maklumat penyewa --}}
            <section class="langkah-borang" data-langkah="6">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Maklumat Penyewa</h2>
                        <p class="text-secondary small mb-0">
                            Tambah maklumat penyewa jika premis bangunan sedang atau pernah disewakan.
                        </p>
                    </div>

                    <button type="button" class="btn btn-utama" data-tambah-penyewa>
                        <i class="bi bi-person-plus me-1"></i> Tambah Penyewa
                    </button>
                </div>

                <div
                    class="row g-3"
                    data-senarai-penyewa
                    data-indeks-seterusnya="{{ $penyewaAktif->count() }}"
                >
                    @forelse ($penyewaAktif as $indeks => $penyewa)
                        @include('bangunan.penyewa', [
                            'indeks' => $indeks,
                            'penyewa' => $penyewa,
                        ])
                    @empty
                        <div class="col-12" data-mesej-penyewa>
                            <div class="kad-kosong py-4">
                                <div class="ilustrasi-kosong">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h3>Tiada maklumat penyewa</h3>
                                <p>Tekan butang “Tambah Penyewa” jika aset ini mempunyai penyewa.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <template id="templatPenyewa">
                    <div class="col-12" data-penyewa>
                        <div class="kad-penyewa">
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
                                        name="penyewa[__INDEX__][tnt_name]"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">No. Kad Pengenalan / No. Syarikat</label>
                                    <input
                                        type="text"
                                        name="penyewa[__INDEX__][tnt_ic_no]"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">No. Akaun Penyewa</label>
                                    <input
                                        type="text"
                                        name="penyewa[__INDEX__][tnt_acc_no]"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tarikh Mula Sewa</label>
                                    <input
                                        type="date"
                                        name="penyewa[__INDEX__][tnt_start_date]"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tarikh Tamat Sewa</label>
                                    <input
                                        type="date"
                                        name="penyewa[__INDEX__][tnt_end_date]"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Status Penyewa</label>
                                    <select name="penyewa[__INDEX__][tnt_status]" class="form-select">
                                        <option value="AKTIF">Aktif</option>
                                        <option value="TIDAK_AKTIF">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" data-sebelum>
                        <i class="bi bi-arrow-left me-1"></i> Sebelum
                    </button>

                    <button type="submit" class="btn btn-utama">
                        <i class="bi bi-check2-circle me-1"></i>
                        {{ $bangunan ? 'Kemaskini Aset Bangunan' : 'Simpan Aset Bangunan' }}
                    </button>
                </div>
            </section>
    </form>
</div>