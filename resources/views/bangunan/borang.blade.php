@php
    $bangunan = $bangunan ?? null;
    $penyewaAktif = $bangunan?->penyewa?->where('tnt_status', 'AKTIF')->values() ?? collect();

    $komponenPemeriksaan = app(\App\Services\Pemeriksaan::class)->komponenAktif();
    $bolehIsiPemeriksaan = \App\Models\TetapanPenyelenggaraan::bolehIsi(auth()->user());
    $pemeriksaanTerkini = $bangunan?->penyelenggaraanTerkini;
    $modPemeriksaan = old('pemeriksaan.mod', $bangunan ? 'TIADA' : 'BARU');
@endphp

<div class="kad-data borang-aset">
    <form method="POST" action="{{ $action }}" data-borang-bangunan enctype="multipart/form-data">
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
                    'Status & Penyelenggaraan',
                    'Pengumpulan & Pengesahan',
                    'Penyewa',
                    'Dokumen',
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
                        <label for="asb_no_file" class="form-label fw-semibold">
                            No. Fail
                        </label>

                        <input
                            type="text"
                            id="asb_no_file"
                            name="asb_no_file"
                            class="form-control @error('asb_no_file') is-invalid @enderror"
                            value="{{ old('asb_no_file', $bangunan?->asb_no_file) }}"
                            placeholder="Contoh: MDM/PH/01/2026"
                        >

                        @error('asb_no_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Poskod</label>
                        <input
                            type="text"
                            name="asb_postcode"
                            class="form-control"
                            value="{{ old('asb_postcode', $bangunan?->asb_postcode) }}"
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Daerah</label>
                        <input
                            type="text"
                            name="asb_distinct"
                            class="form-control bg-light"
                            value="Marang"
                            readonly
                            aria-readonly="true"
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="asbMukim" class="form-label fw-semibold">Mukim</label>

                        <select
                            id="asbMukim"
                            name="asb_mukim"
                            class="form-select @error('asb_mukim') is-invalid @enderror"
                        >
                            <option value="">Pilih Mukim</option>

                            @foreach ([
                                'Alor Limbat',
                                'Bukit Payung',
                                'Jerong',
                                'Merchang',
                                'Pulau Kerengga',
                                'Rusila',
                            ] as $mukim)
                                <option
                                    value="{{ $mukim }}"
                                    @selected(old('asb_mukim', $bangunan?->asb_mukim) === $mukim)
                                >
                                    {{ $mukim }}
                                </option>
                            @endforeach
                        </select>

                        @error('asb_mukim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Bandar</label>
                        <input type="text" name="asb_city" class="form-control"
                            value="{{ old('asb_city', $bangunan?->asb_city) }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">Negeri</label>
                        <input
                            type="text"
                            name="asb_state"
                            class="form-control bg-light"
                            value="Terengganu"
                            readonly
                            aria-readonly="true"
                        >
                    </div>

                    <div class="col-12 col-md-6">
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
                        <label class="form-label fw-semibold">Keluasan Bangunan</label>

                        <div class="input-group">
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                name="asb_size"
                                class="form-control"
                                value="{{ old('asb_size', $bangunan?->asb_size) }}"
                            >
                            <span class="input-group-text">m²</span>
                        </div>
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

            {{-- Langkah 5: Status dan penyelenggaraan --}}
            <section class="langkah-borang" data-langkah="5">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Status & Penyelenggaraan</h2>
                    <p class="text-secondary small mb-0">
                        Rekodkan status semasa aset serta keputusan pemeriksaan penyelenggaraan.
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="asb_maintenance_status" class="form-label">
                            Status Penyelenggaraan <span class="text-danger">*</span>
                        </label>
                        <select
                            id="asb_maintenance_status"
                            name="asb_maintenance_status"
                            class="form-select @error('asb_maintenance_status') is-invalid @enderror"
                            required
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
                            Status Premis <span class="text-danger">*</span>
                        </label>
                        <select
                            id="asb_premise_status"
                            name="asb_premise_status"
                            class="form-select @error('asb_premise_status') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Pilih Status --</option>

                            @foreach ([
                                'SEWA' => 'Sewa',
                                'TIDAK_DISEWA' => 'Tidak Disewa',
                                'PAJAK' => 'Pajak',
                                'TIDAK_DIPAJAK' => 'Tidak Dipajak',
                                'KEGUNAAN_MDM' => 'Kegunaan MDM',
                                'DILUPUS' => 'Dilupus',
                            ] as $nilai => $label)
                                <option
                                    value="{{ $nilai }}"
                                    @selected(old('asb_premise_status', $bangunan?->asb_premise_status) === $nilai)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('asb_premise_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PEMERIKSAAN PENYELENGGARAAN --}}
                    @if ($komponenPemeriksaan->isNotEmpty())
                        <div class="col-12">
                            <h3 class="subtajuk-seksyen">Pemeriksaan Penyelenggaraan</h3>
                        </div>

                        @if (! $bolehIsiPemeriksaan)
                            <input type="hidden" name="pemeriksaan[mod]" value="TIADA">

                            <div class="col-12">
                                <div class="alert alert-light border small mb-0">
                                    <i class="bi bi-lock me-1"></i>
                                    Anda tidak mempunyai akses untuk mengisi rekod pemeriksaan penyelenggaraan.
                                </div>
                            </div>
                        @else
                            @php
                                $pilihanMod = [
                                    'BARU' => [
                                        'ikon' => 'bi-plus-circle',
                                        'tajuk' => $bangunan ? 'Tambah pemeriksaan baru' : 'Isi pemeriksaan sekarang',
                                        'penerangan' => $bangunan ? 'Simpan sebagai rekod baharu' : 'Rekod pemeriksaan pertama aset',
                                    ],
                                ];

                                if ($pemeriksaanTerkini) {
                                    $pilihanMod['KEMASKINI'] = [
                                        'ikon' => 'bi-pencil-square',
                                        'tajuk' => 'Kemas kini terkini',
                                        'penerangan' => 'Ubah rekod ' . $pemeriksaanTerkini->asm_inspection_date->format('d/m/Y'),
                                    ];
                                }

                                $pilihanMod['TIADA'] = [
                                    'ikon' => $bangunan ? 'bi-dash-circle' : 'bi-clock-history',
                                    'tajuk' => $bangunan ? 'Tiada perubahan' : 'Isi kemudian',
                                    'penerangan' => $bangunan ? 'Pemeriksaan tidak diubah' : 'Boleh diisi melalui kemas kini',
                                ];

                                $nilaiPemeriksaan = [
                                    'BAIK' => ['kelas' => 'baik', 'ikon' => 'bi-check-circle'],
                                    'ROSAK' => ['kelas' => 'rosak', 'ikon' => 'bi-exclamation-triangle'],
                                    'TIDAK_BERKAITAN' => ['kelas' => 'tb', 'ikon' => 'bi-dash-circle'],
                                ];

                                $jumlahItemPemeriksaan = $komponenPemeriksaan->sum(
                                    fn ($komponen) => count($komponen->itemAktif())
                                );
                            @endphp

                            <div
                                class="col-12"
                                data-pemeriksaan
                                data-tarikh-terkini="{{ $pemeriksaanTerkini?->asm_inspection_date?->format('Y-m-d') }}"
                                data-catatan-terkini="{{ $pemeriksaanTerkini?->asm_note }}"
                            >
                                {{-- Pilihan mod --}}
                                <div class="row g-2 mb-2">
                                    @foreach ($pilihanMod as $nilai => $mod)
                                        <div class="col-12 col-md">
                                            <input
                                                type="radio"
                                                class="btn-check"
                                                name="pemeriksaan[mod]"
                                                id="mod{{ $nilai }}"
                                                value="{{ $nilai }}"
                                                autocomplete="off"
                                                @checked($modPemeriksaan === $nilai)
                                            >
                                            <label class="pilihan-mod" for="mod{{ $nilai }}">
                                                <i class="bi {{ $mod['ikon'] }}"></i>
                                                <span>
                                                    <strong>{{ $mod['tajuk'] }}</strong>
                                                    <small>{{ $mod['penerangan'] }}</small>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <p class="small text-secondary mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    @if ($pemeriksaanTerkini)
                                        Pemeriksaan terkini pada
                                        <strong>{{ $pemeriksaanTerkini->asm_inspection_date->format('d/m/Y') }}</strong>
                                        oleh {{ $pemeriksaanTerkini->pemeriksa?->usr_name ?? '-' }}.
                                    @elseif ($bangunan)
                                        Belum ada rekod pemeriksaan bagi aset ini.
                                    @else
                                        Pemeriksaan boleh diisi sekarang atau kemudian melalui kemas kini aset.
                                    @endif
                                </p>

                                <fieldset
                                    data-isi-pemeriksaan
                                    @class(['d-none' => $modPemeriksaan === 'TIADA'])
                                    @disabled($modPemeriksaan === 'TIADA')
                                >
                                    {{-- Maklumat pemeriksaan + kemajuan --}}
                                    <div class="panel-pemeriksaan mb-3">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-12 col-md-4">
                                                <label for="asm_inspection_date" class="form-label small fw-semibold">
                                                    Tarikh Pemeriksaan <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="date"
                                                    id="asm_inspection_date"
                                                    name="pemeriksaan[asm_inspection_date]"
                                                    max="{{ now()->format('Y-m-d') }}"
                                                    value="{{ old(
                                                        'pemeriksaan.asm_inspection_date',
                                                        $modPemeriksaan === 'KEMASKINI'
                                                            ? $pemeriksaanTerkini?->asm_inspection_date?->format('Y-m-d')
                                                            : now()->format('Y-m-d')
                                                    ) }}"
                                                    class="form-control @error('pemeriksaan.asm_inspection_date') is-invalid @enderror"
                                                    data-tarikh-pemeriksaan
                                                    required
                                                >

                                                @error('pemeriksaan.asm_inspection_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <span class="form-label small fw-semibold d-block">Pemeriksa</span>
                                                <div class="cip-pemeriksa" title="Direkodkan secara automatik">
                                                    <span class="avatar-pengguna">
                                                        {{ strtoupper(substr(auth()->user()->usr_name, 0, 1)) }}
                                                    </span>
                                                    <span class="text-truncate">{{ auth()->user()->usr_name }}</span>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <div
                                                    class="kemajuan-pemeriksaan"
                                                    data-kemajuan-pemeriksaan
                                                >
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="fw-semibold">Kemajuan</span>
                                                        <span class="text-secondary">
                                                            <strong data-bil-diisi>0</strong> / {{ $jumlahItemPemeriksaan }} item
                                                        </span>
                                                    </div>

                                                    <div class="progress" role="progressbar" aria-label="Kemajuan pemeriksaan">
                                                        <div class="progress-bar" style="width: 0%" data-bar-kemajuan></div>
                                                    </div>

                                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                                        <span class="kiraan-nilai baik">Baik <span data-bil="BAIK">0</span></span>
                                                        <span class="kiraan-nilai rosak">Rosak <span data-bil="ROSAK">0</span></span>
                                                        <span class="kiraan-nilai tb">T/B <span data-bil="TIDAK_BERKAITAN">0</span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Komponen --}}
                                    @foreach ($komponenPemeriksaan as $komponen)
                                        <div class="kad-komponen-pemeriksaan" data-komponen-pemeriksaan>
                                            <div class="kepala-komponen-pemeriksaan">
                                                <span class="nombor-komponen">{{ $loop->iteration }}</span>

                                                <div class="flex-grow-1 min-w-0">
                                                    <strong>{{ $komponen->amc_name }}</strong>
                                                    <small>
                                                        <span data-kiraan-komponen>0</span> / {{ count($komponen->itemAktif()) }} item diisi
                                                    </small>
                                                </div>

                                                <div class="d-flex flex-shrink-0 gap-2">
                                                    <button type="button" class="btn btn-sm butang-semua-baik" data-semua-baik>
                                                        <i class="bi bi-check2-all me-1"></i>Semua Baik
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="btn btn-sm butang-padam-pilihan"
                                                        data-padam-pilihan
                                                        title="Kosongkan semua pilihan dalam komponen ini"
                                                    >
                                                        <i class="bi bi-eraser me-1"></i>Padam
                                                    </button>
                                                </div>
                                            </div>

                                            @foreach ($komponen->itemAktif() as $item)
                                                @php
                                                    $namaInput = "pemeriksaan[nilai][{$komponen->amc_id}][{$item['id']}]";
                                                    $kunciOld = "pemeriksaan.nilai.{$komponen->amc_id}.{$item['id']}";
                                                    $nilaiTerkini = $pemeriksaanTerkini?->nilaiItem($komponen->amc_id, $item['id']);
                                                    $nilaiSemasa = old(
                                                        $kunciOld,
                                                        $modPemeriksaan === 'KEMASKINI' ? $nilaiTerkini : null
                                                    );
                                                @endphp

                                                <div class="baris-pemeriksaan" data-baris-pemeriksaan data-nilai-terkini="{{ $nilaiTerkini }}">
                                                    <div class="nama-item-pemeriksaan">
                                                        {{ $item['nama'] }}

                                                        @error($kunciOld)
                                                            <small class="text-danger d-block">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div
                                                        class="kumpulan-nilai"
                                                        role="radiogroup"
                                                        aria-label="Keputusan {{ $item['nama'] }}"
                                                    >
                                                        @foreach ($nilaiPemeriksaan as $nilai => $gaya)
                                                            @php
                                                                $idInput = "p{$komponen->amc_id}{$item['id']}{$nilai}";
                                                            @endphp

                                                            <input
                                                                type="radio"
                                                                class="btn-check"
                                                                name="{{ $namaInput }}"
                                                                id="{{ $idInput }}"
                                                                value="{{ $nilai }}"
                                                                autocomplete="off"
                                                                @checked($nilaiSemasa === $nilai)
                                                                required
                                                            >
                                                            <label class="pilihan-nilai {{ $gaya['kelas'] }}" for="{{ $idInput }}">
                                                                <i class="bi {{ $gaya['ikon'] }}"></i>
                                                                {{ \App\Support\Paparan::label($nilai) }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach

                                    {{-- Catatan --}}
                                    <div class="panel-pemeriksaan">
                                        <label for="asm_note" class="form-label small fw-semibold">
                                            <i class="bi bi-journal-text me-1"></i>Catatan Keseluruhan
                                            <span class="text-secondary fw-normal">(pilihan)</span>
                                        </label>
                                        <textarea
                                            id="asm_note"
                                            name="pemeriksaan[asm_note]"
                                            rows="3"
                                            maxlength="2000"
                                            class="form-control @error('pemeriksaan.asm_note') is-invalid @enderror"
                                            placeholder="Ulasan keseluruhan, cadangan tindakan atau maklumat tambahan pemeriksaan"
                                            data-catatan-pemeriksaan
                                        >{{ old(
                                            'pemeriksaan.asm_note',
                                            $modPemeriksaan === 'KEMASKINI' ? $pemeriksaanTerkini?->asm_note : ''
                                        ) }}</textarea>

                                        @error('pemeriksaan.asm_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>
                        @endif
                    @else
                        <input type="hidden" name="pemeriksaan[mod]" value="TIADA">
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3">
                    <button type="button" class="btn btn-outline-secondary" data-sebelum>
                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                    </button>

                    <button type="button" class="btn btn-utama" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </section>

            {{-- Langkah 6: Pengumpulan dan pengesahan --}}
            <section class="langkah-borang" data-langkah="6">
                <div class="mb-4">
                    <h2 class="h5 fw-bold mb-1">Pengumpulan & Pengesahan</h2>
                    <p class="text-secondary small mb-0">
                        Rekodkan maklumat pegawai yang mengumpul dan mengesahkan data aset.
                    </p>
                </div>

                <div class="row g-3">
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
                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                    </button>

                    <button type="button" class="btn btn-utama" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </section>

            {{-- Langkah 7: Maklumat penyewa --}}
            <section class="langkah-borang" data-langkah="7">
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
                                    <label class="form-label">
                                        No. Kad Pengenalan / No. Syarikat <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="penyewa[__INDEX__][tnt_ic_no]"
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
                                        name="penyewa[__INDEX__][tnt_acc_no]"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tarikh Mula Perjanjian</label>
                                    <input
                                        type="date"
                                        name="penyewa[__INDEX__][tnt_mula_perjanjian]"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tarikh Tamat Perjanjian</label>
                                    <input
                                        type="date"
                                        name="penyewa[__INDEX__][tnt_tamat_perjanjian]"
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
                        <i class="bi bi-arrow-left me-1"></i> Sebelumnya
                    </button>

                    <button type="button" class="btn btn-utama" data-seterusnya>
                        Seterusnya <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </section>
            {{-- Langkah 8: Dokumen --}}
            <section class="langkah-borang" data-langkah="8">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Dokumen Aset Bangunan</h2>
                        <p class="text-secondary small mb-0">
                            Muat naik dokumen berkaitan seperti geran, pelan, surat kelulusan,
                            perjanjian sewa atau gambar bangunan.
                        </p>
                    </div>

                    <button type="button" class="btn btn-utama" data-tambah-dokumen>
                        <i class="bi bi-file-earmark-plus me-1"></i>Tambah Dokumen
                    </button>
                </div>

                <div class="alert alert-light border small mb-4">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    Dokumen adalah pilihan. Format dibenarkan: PDF, JPG, JPEG, PNG dan WEBP.
                    Saiz maksimum setiap fail ialah 10MB.
                </div>

                <div class="row g-3" data-senarai-dokumen data-indeks-seterusnya="0">
                    <div class="col-12" data-mesej-dokumen>
                        <div class="kad-kosong py-4">
                            <div class="ilustrasi-kosong">
                                <i class="bi bi-folder2-open"></i>
                            </div>

                            <h3>Tiada dokumen baharu</h3>
                            <p>Tekan “Tambah Dokumen” jika ada dokumen untuk dimuat naik.</p>
                        </div>
                    </div>
                </div>

                @if ($bangunan && $bangunan->dokumen->isNotEmpty())
                    <div class="mt-4 pt-4 border-top">
                        <h3 class="subtajuk-seksyen mb-3">
                            Dokumen Sedia Ada
                        </h3>

                        <div class="row g-3">
                            @foreach ($bangunan->dokumen as $dokumen)
                                <div class="col-12 col-md-6">
                                    <div class="kad-dokumen">
                                        <div class="d-flex gap-3 align-items-start">
                                            <div class="ikon-dokumen">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </div>

                                            <div class="flex-grow-1 min-w-0">
                                                <span class="lencana-dokumen">
                                                    {{ str_replace('_', ' ', $dokumen->asbdoc_type) }}
                                                </span>

                                                <p class="fw-semibold text-truncate mb-1 mt-2">
                                                    {{ $dokumen->asbdoc_name }}
                                                </p>

                                                <small class="text-secondary d-block">
                                                    Dimuat naik:
                                                    {{ $dokumen->uploaded_at?->format('d/m/Y, h:i A') ?? '-' }}
                                                </small>

                                                <div class="d-flex gap-2 mt-3">
                                                    <a
                                                        href="{{ route('bangunan.dokumen.lihat', [$bangunan, $dokumen]) }}"
                                                        target="_blank"
                                                        rel="noopener"
                                                        class="btn btn-sm btn-outline-primary"
                                                    >
                                                        <i class="bi bi-eye me-1"></i>Lihat
                                                    </a>

                                                    <a
                                                        href="{{ route('bangunan.dokumen.lihat', [$bangunan, $dokumen]) }}"
                                                        download="{{ $dokumen->asbdoc_name }}"
                                                        class="btn btn-sm btn-outline-secondary"
                                                    >
                                                        <i class="bi bi-download"></i>
                                                    </a>

                                                    <button
                                                        type="submit"
                                                        form="formPadamDokumen{{ $dokumen->asbdoc_id }}"
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

                <template id="templatDokumen">
                    <div class="col-12" data-dokumen>
                        <div class="kad-dokumen-baharu">
                            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                                <h3 class="subtajuk-seksyen mb-0">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>
                                    Dokumen Baharu
                                </h3>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    data-buang-dokumen
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
                                        <option value="PELAN">Pelan Bangunan</option>
                                        <option value="SURAT_KELULUSAN">Surat Kelulusan</option>
                                        <option value="PERJANJIAN_SEWA">Perjanjian Sewa</option>
                                        <option value="LAPORAN_PENILAIAN">Laporan Penilaian</option>
                                        <option value="GAMBAR">Gambar Bangunan</option>
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

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" data-sebelum>
                        <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                    </button>

                    <button type="submit" class="btn btn-utama">
                        <i class="bi bi-check2-circle me-1"></i>
                        {{ $bangunan ? 'Kemaskini Aset Bangunan' : 'Simpan Aset Bangunan' }}
                    </button>
                </div>
            </section>
    </form>
    @if ($bangunan)
        @foreach ($bangunan->dokumen as $dokumen)
            <form
                id="formPadamDokumen{{ $dokumen->asbdoc_id }}"
                method="POST"
                action="{{ route('bangunan.dokumen.hapus', [$bangunan, $dokumen]) }}"
                data-padam-dokumen
                data-nama-dokumen="{{ $dokumen->asbdoc_name }}"
            >
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif
</div>

@push('skrip')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bekas = document.querySelector('[data-pemeriksaan]');

            if (! bekas) {
                return;
            }

            const fieldset = bekas.querySelector('[data-isi-pemeriksaan]');
            const tarikh = bekas.querySelector('[data-tarikh-pemeriksaan]');
            const catatan = bekas.querySelector('[data-catatan-pemeriksaan]');
            const kemajuan = bekas.querySelector('[data-kemajuan-pemeriksaan]');

            const sekarang = new Date();
            const hariIni = new Date(sekarang.getTime() - sekarang.getTimezoneOffset() * 60000)
                .toISOString()
                .slice(0, 10);

            /*
            | Kemajuan pengisian
            */
            const kemaskiniKemajuan = () => {
                const semuaBaris = [...bekas.querySelectorAll('[data-baris-pemeriksaan]')];
                const kiraan = { BAIK: 0, ROSAK: 0, TIDAK_BERKAITAN: 0 };
                let diisi = 0;

                semuaBaris.forEach((baris) => {
                    const dipilih = baris.querySelector('input:checked');

                    baris.classList.toggle('rosak', dipilih?.value === 'ROSAK');

                    if (dipilih) {
                        diisi++;
                        kiraan[dipilih.value]++;
                    }
                });

                bekas.querySelectorAll('[data-komponen-pemeriksaan]').forEach((komponen) => {
                    komponen.querySelector('[data-kiraan-komponen]').textContent =
                        komponen.querySelectorAll('[data-baris-pemeriksaan] input:checked').length;
                });

                if (! kemajuan) {
                    return;
                }

                const jumlah = semuaBaris.length;
                const peratus = jumlah ? Math.round((diisi / jumlah) * 100) : 0;
                const bar = kemajuan.querySelector('[data-bar-kemajuan]');

                kemajuan.querySelector('[data-bil-diisi]').textContent = diisi;
                bar.style.width = `${peratus}%`;
                bar.classList.toggle('bg-success', jumlah > 0 && diisi === jumlah);

                Object.entries(kiraan).forEach(([nilai, bil]) => {
                    kemajuan.querySelector(`[data-bil="${nilai}"]`).textContent = bil;
                });
            };

            /*
            | Tukar mod pemeriksaan
            */
            const tukarMod = (mod, tetapkanNilai = true) => {
                const tiada = mod === 'TIADA';

                // fieldset disabled: medan tidak disahkan dan tidak dihantar
                fieldset.disabled = tiada;
                fieldset.classList.toggle('d-none', tiada);

                if (! tetapkanNilai) {
                    return;
                }

                if (mod === 'BARU') {
                    tarikh.value = hariIni;
                    catatan.value = '';

                    // Pemeriksaan baharu: kosongkan semua pilihan item
                    bekas.querySelectorAll('[data-baris-pemeriksaan] input:checked').forEach((input) => {
                        input.checked = false;
                    });
                }

                if (mod === 'KEMASKINI') {
                    tarikh.value = bekas.dataset.tarikhTerkini || '';
                    catatan.value = bekas.dataset.catatanTerkini || '';

                    // Kemas kini: isi semula pilihan item daripada rekod terkini
                    bekas.querySelectorAll('[data-baris-pemeriksaan]').forEach((baris) => {
                        const nilai = baris.dataset.nilaiTerkini;

                        baris.querySelectorAll('input[type="radio"]').forEach((input) => {
                            input.checked = nilai !== '' && input.value === nilai;
                        });
                    });
                }

                kemaskiniKemajuan();
            };

            bekas.querySelectorAll('input[type="radio"][name="pemeriksaan[mod]"]').forEach((pilihan) => {
                pilihan.addEventListener('change', () => tukarMod(pilihan.value));
            });

            const modDipilih = bekas.querySelector('input[type="radio"][name="pemeriksaan[mod]"]:checked');

            if (modDipilih) {
                tukarMod(modDipilih.value, false);
            }

            /*
            | Pilihan nilai item + butang Semua Baik
            */
            bekas.addEventListener('change', (event) => {
                if (event.target.closest('[data-baris-pemeriksaan]')) {
                    kemaskiniKemajuan();
                }
            });

            bekas.querySelectorAll('[data-semua-baik]').forEach((butang) => {
                butang.addEventListener('click', () => {
                    butang
                        .closest('[data-komponen-pemeriksaan]')
                        .querySelectorAll('input[value="BAIK"]')
                        .forEach((input) => {
                            input.checked = true;
                        });

                    kemaskiniKemajuan();
                });
            });

            bekas.querySelectorAll('[data-padam-pilihan]').forEach((butang) => {
                butang.addEventListener('click', () => {
                    butang
                        .closest('[data-komponen-pemeriksaan]')
                        .querySelectorAll('[data-baris-pemeriksaan] input:checked')
                        .forEach((input) => {
                            input.checked = false;
                        });

                    kemaskiniKemajuan();
                });
            });

            kemaskiniKemajuan();
        });
    </script>
@endpush