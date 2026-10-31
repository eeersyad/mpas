@extends('layouts.utama')

@section('tajuk', 'Tetapan Komponen Penyelenggaraan')

@section('kandungan')
    <div class="hero-tetapan mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
    </div>

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            {{-- Ralat pengesahan --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong class="d-block mb-2">Tetapan tidak dapat disimpan:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $ralat)
                            <li>{{ $ralat }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kepala + butang tambah --}}
            <div class="kad-data kad-tetapan mb-4">
                <div class="kepala-kad-tetapan">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-tools"></i>
                            </div>

                            <div>
                                <h2 class="h6 fw-bold mb-1">Komponen Penyelenggaraan</h2>
                                <p class="mb-0 text-secondary small">
                                    Senarai komponen dan item yang akan dipaparkan dalam
                                    borang pemeriksaan aset bangunan.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-utama"
                            data-bs-toggle="collapse"
                            data-bs-target="#borangKomponenBaru"
                            aria-expanded="false"
                            aria-controls="borangKomponenBaru"
                        >
                            <i class="bi bi-plus-circle me-1"></i>Tambah Komponen
                        </button>
                    </div>
                </div>

                {{-- Borang komponen baru --}}
                <div class="collapse" id="borangKomponenBaru">
                    <form method="POST" action="{{ route('tetapan.komponen.simpan') }}" class="isi-kad-tetapan border-top">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nama Komponen</label>
                                <input
                                    type="text"
                                    name="amc_name"
                                    class="form-control"
                                    value="{{ old('amc_name') }}"
                                    placeholder="Contoh: Sistem Keselamatan"
                                    required
                                >
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Turutan</label>
                                <input
                                    type="number"
                                    min="1"
                                    name="amc_order"
                                    class="form-control"
                                    value="{{ old('amc_order', $komponen->count() + 1) }}"
                                    required
                                >
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="amc_status" class="form-select">
                                    <option value="ACTIVE">Aktif</option>
                                    <option value="INACTIVE">Tidak aktif</option>
                                </select>
                            </div>
                        </div>

                        <label class="form-label fw-semibold">Senarai Item</label>

                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">Turutan</th>
                                        <th>Nama Item</th>
                                        <th style="width: 170px;">Status</th>
                                        <th style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemKomponenBaru"></tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-tambah-item="#itemKomponenBaru"
                            >
                                <i class="bi bi-plus-lg me-1"></i>Tambah Item
                            </button>

                            <button type="submit" class="btn btn-utama">
                                <i class="bi bi-check-lg me-1"></i>Simpan Komponen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Senarai komponen sedia ada --}}
            @forelse ($komponen as $item)
                <form
                    method="POST"
                    action="{{ route('tetapan.komponen.kemaskini', $item) }}"
                    class="kad-data kad-tetapan mb-4"
                >
                    @csrf
                    @method('PUT')

                    <div class="kepala-kad-tetapan">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <p class="teks-kecil mb-1">KOMPONEN {{ $loop->iteration }}</p>
                                <h2 class="h6 fw-bold mb-0">{{ $item->amc_name }}</h2>
                            </div>

                            <span class="lencana-jadual {{ $item->amc_status === 'ACTIVE' ? 'utama' : 'kelabu' }}">
                                {{ \App\Support\Paparan::label($item->amc_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="isi-kad-tetapan">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nama Komponen</label>
                                <input
                                    type="text"
                                    name="amc_name"
                                    class="form-control"
                                    value="{{ $item->amc_name }}"
                                    required
                                >
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Turutan</label>
                                <input
                                    type="number"
                                    min="1"
                                    name="amc_order"
                                    class="form-control"
                                    value="{{ $item->amc_order }}"
                                    required
                                >
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="amc_status" class="form-select">
                                    <option value="ACTIVE" @selected($item->amc_status === 'ACTIVE')>Aktif</option>
                                    <option value="INACTIVE" @selected($item->amc_status === 'INACTIVE')>Tidak aktif</option>
                                </select>
                            </div>
                        </div>

                        <label class="form-label fw-semibold">
                            Senarai Item
                            <small class="text-secondary fw-normal">({{ count($item->itemAktif()) }} aktif)</small>
                        </label>

                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">Turutan</th>
                                        <th>Nama Item</th>
                                        <th style="width: 170px;">Status</th>
                                        <th style="width: 60px;"></th>
                                    </tr>
                                </thead>

                                <tbody id="itemKomponen{{ $item->amc_id }}">
                                    @foreach ($item->amc_items ?? [] as $index => $baris)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $baris['id'] }}">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    name="items[{{ $index }}][turutan]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $index + 1 }}"
                                                    required
                                                >
                                            </td>
                                            <td>
                                                <input
                                                    type="text"
                                                    name="items[{{ $index }}][nama]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $baris['nama'] }}"
                                                    required
                                                >
                                            </td>
                                            <td>
                                                <select name="items[{{ $index }}][status]" class="form-select form-select-sm">
                                                    <option value="ACTIVE" @selected(($baris['status'] ?? 'ACTIVE') === 'ACTIVE')>Aktif</option>
                                                    <option value="INACTIVE" @selected(($baris['status'] ?? 'ACTIVE') === 'INACTIVE')>Tidak aktif</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <i class="bi bi-lock text-secondary" title="Item sedia ada tidak boleh dibuang. Nyahaktifkan jika tidak digunakan."></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-tambah-item="#itemKomponen{{ $item->amc_id }}"
                            >
                                <i class="bi bi-plus-lg me-1"></i>Tambah Item
                            </button>

                            <button type="submit" class="btn btn-utama">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            @empty
                <div class="kad-kosong">
                    <div class="ilustrasi-kosong">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h3>Tiada komponen penyelenggaraan</h3>
                    <p>Klik butang Tambah Komponen untuk mendaftarkan komponen pertama.</p>
                </div>
            @endforelse

            <div class="makluman-tetapan">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Komponen dan item tidak boleh dihapus supaya rekod pemeriksaan lama kekal lengkap.
                Tukar status kepada <strong>Tidak aktif</strong> untuk menyembunyikannya daripada
                borang pemeriksaan baru.
            </div>
        </div>
    </div>

    {{-- Templat baris item baru (disalin oleh JavaScript) --}}
    <template id="templatItem">
        <tr>
            <td>
                <input
                    type="number"
                    min="1"
                    name="items[__INDEX__][turutan]"
                    class="form-control form-control-sm"
                    value="__TURUTAN__"
                    required
                >
            </td>
            <td>
                <input
                    type="text"
                    name="items[__INDEX__][nama]"
                    class="form-control form-control-sm"
                    placeholder="Nama item"
                    required
                >
            </td>
            <td>
                <select name="items[__INDEX__][status]" class="form-select form-select-sm">
                    <option value="ACTIVE">Aktif</option>
                    <option value="INACTIVE">Tidak aktif</option>
                </select>
            </td>
            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    data-buang-item
                    title="Buang baris"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('skrip')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const templat = document.getElementById('templatItem');

            const tambahBaris = (sasaran) => {
                const index = 'baru' + Date.now() + Math.floor(Math.random() * 1000);
                const turutan = sasaran.children.length + 1;

                const html = templat.innerHTML
                    .replaceAll('__INDEX__', index)
                    .replaceAll('__TURUTAN__', turutan);

                sasaran.insertAdjacentHTML('beforeend', html);
                sasaran.lastElementChild.querySelector('input[type="text"]')?.focus();
            };

            document.querySelectorAll('[data-tambah-item]').forEach((butang) => {
                butang.addEventListener('click', () => {
                    tambahBaris(document.querySelector(butang.dataset.tambahItem));
                });
            });

            // Borang komponen baru bermula dengan satu baris kosong
            const itemBaru = document.getElementById('itemKomponenBaru');
            if (itemBaru && itemBaru.children.length === 0) {
                tambahBaris(itemBaru);
            }

            // Buang baris baru (hanya baris yang belum disimpan ada butang ini)
            document.addEventListener('click', (event) => {
                const butang = event.target.closest('[data-buang-item]');

                if (butang) {
                    butang.closest('tr').remove();
                }
            });
        });
    </script>
@endpush