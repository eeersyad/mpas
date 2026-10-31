{{--
    Parameter:
    $idModal  - ID unik modal (contoh: modalKomponenBaru, modalKomponen3)
    $tajuk    - Tajuk modal
    $action   - URL hantar borang
    $method   - 'POST' atau 'PUT'
    $komponen - Model KomponenPenyelenggaraan atau null (komponen baru)
    $turutanLalai - Turutan lalai untuk komponen baru
--}}
@php
    // Jika borang ini gagal disahkan, guna nilai old() supaya input tidak hilang
    $gunaOld = old('_modal') === $idModal;

    $nilaiNama = $gunaOld ? old('amc_name') : ($komponen?->amc_name ?? '');
    $nilaiTurutan = $gunaOld ? old('amc_order') : ($komponen?->amc_order ?? $turutanLalai);
    $nilaiStatus = $gunaOld ? old('amc_status', 'INACTIVE') : ($komponen?->amc_status ?? 'ACTIVE');

    $senaraiItem = $gunaOld
        ? array_values(old('items', []))
        : ($komponen?->amc_items ?? [['id' => '', 'nama' => '', 'status' => 'ACTIVE']]);
@endphp

<div
    class="modal fade"
    id="{{ $idModal }}"
    tabindex="-1"
    aria-labelledby="{{ $idModal }}Tajuk"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form
            method="POST"
            action="{{ $action }}"
            class="modal-content modal-komponen"
            data-borang-komponen
        >
            @csrf

            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <input type="hidden" name="_modal" value="{{ $idModal }}">

            <div class="modal-header">
                <div>
                    <small class="d-block fw-semibold">KOMPONEN PENYELENGGARAAN</small>
                    <h5 class="modal-title fw-bold mb-0" id="{{ $idModal }}Tajuk">{{ $tajuk }}</h5>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                @if ($gunaOld && $errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $ralat)
                                <li>{{ $ralat }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Maklumat komponen --}}
                <div class="panel-modal-komponen mb-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-7">
                            <label for="{{ $idModal }}Nama" class="form-label fw-semibold small">
                                Nama Komponen <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="{{ $idModal }}Nama"
                                name="amc_name"
                                value="{{ $nilaiNama }}"
                                class="form-control"
                                placeholder="Contoh: Sistem Keselamatan"
                                maxlength="255"
                                required
                            >
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="{{ $idModal }}Turutan" class="form-label fw-semibold small">Turutan</label>
                            <input
                                type="number"
                                id="{{ $idModal }}Turutan"
                                name="amc_order"
                                value="{{ $nilaiTurutan }}"
                                min="1"
                                max="999"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small d-block">Status</label>
                            <input type="hidden" name="amc_status" value="INACTIVE">
                            <div class="form-check form-switch mb-2">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="{{ $idModal }}Status"
                                    name="amc_status"
                                    value="ACTIVE"
                                    @checked($nilaiStatus === 'ACTIVE')
                                >
                                <label class="form-check-label small" for="{{ $idModal }}Status">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Senarai item --}}
                <div class="panel-modal-komponen">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0">Senarai Item</h6>
                            <small class="text-secondary">
                                Guna <i class="bi bi-arrow-up"></i><i class="bi bi-arrow-down"></i> untuk susun.
                                Item sedia ada <i class="bi bi-lock"></i> tidak boleh dibuang.
                            </small>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" data-tambah-item>
                            <i class="bi bi-plus-lg me-1"></i>Tambah Item
                        </button>
                    </div>

                    <div class="senarai-item-komponen" data-senarai-item>
                        @foreach ($senaraiItem as $baris)
                            @php
                                $kunci = 'i' . $loop->index;
                                $itemLama = filled($baris['id'] ?? null);
                            @endphp

                            <div class="baris-item-komponen" data-baris-item>
                                <input type="hidden" name="items[{{ $kunci }}][id]" value="{{ $baris['id'] ?? '' }}">
                                <input type="hidden" name="items[{{ $kunci }}][turutan]" value="{{ $loop->iteration }}" data-turutan>

                                <span class="nombor-item" data-nombor>{{ $loop->iteration }}</span>

                                <input
                                    type="text"
                                    name="items[{{ $kunci }}][nama]"
                                    value="{{ $baris['nama'] ?? '' }}"
                                    class="form-control form-control-sm"
                                    placeholder="Nama item"
                                    maxlength="255"
                                    aria-label="Nama item"
                                    required
                                >

                                <input type="hidden" name="items[{{ $kunci }}][status]" value="INACTIVE">
                                <div class="form-check form-switch mb-0" title="Aktif / Tidak aktif">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        name="items[{{ $kunci }}][status]"
                                        value="ACTIVE"
                                        aria-label="Item aktif"
                                        @checked(($baris['status'] ?? 'ACTIVE') === 'ACTIVE')
                                    >
                                </div>

                                <button type="button" class="butang-ikon-kecil" data-naik title="Naik">
                                    <i class="bi bi-arrow-up"></i>
                                </button>
                                <button type="button" class="butang-ikon-kecil" data-turun title="Turun">
                                    <i class="bi bi-arrow-down"></i>
                                </button>

                                @if ($itemLama)
                                    <span class="butang-ikon-kecil" title="Item sedia ada tidak boleh dibuang. Nyahaktifkan jika tidak digunakan.">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                @else
                                    <button type="button" class="butang-ikon-kecil bahaya" data-buang-item title="Buang">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-utama">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>