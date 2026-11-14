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
            {{-- Ralat yang tidak berkait dengan mana-mana modal --}}
            @if ($errors->any() && ! old('_modal'))
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $ralat)
                            <li>{{ $ralat }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kepala --}}
            <div class="kad-data kad-tetapan mb-4">
                <div class="kepala-kad-tetapan">
                    <div class="d-flex align-items-center justify-content-between gap-3 w-100">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-tools"></i>
                            </div>

                            <div class="min-w-0">
                                <h2 class="h6 fw-bold mb-1">Komponen Penyelenggaraan</h2>
                                <p class="mb-0 text-secondary small">
                                    Komponen dan item yang dipaparkan dalam borang pemeriksaan aset bangunan.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-utama butang-tambah-ikon flex-shrink-0"
                            data-bs-toggle="modal"
                            data-bs-target="#modalKomponenBaru"
                            title="Tambah Komponen"
                            aria-label="Tambah Komponen"
                        >
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-4">
                    <div class="statistik-komponen">
                        <span class="ikon hijau"><i class="bi bi-diagram-3-fill"></i></span>
                        <div>
                            <p>Komponen Aktif</p>
                            <strong>{{ $jumlahKomponenAktif }} <small class="fs-6 text-secondary fw-semibold">/ {{ $komponen->count() }}</small></strong>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="statistik-komponen">
                        <span class="ikon biru"><i class="bi bi-check2-square"></i></span>
                        <div>
                            <p>Item Aktif</p>
                            <strong>{{ $jumlahItemAktif }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="statistik-komponen">
                        <span class="ikon jingga"><i class="bi bi-slash-circle"></i></span>
                        <div>
                            <p>Item Tidak Aktif</p>
                            <strong>{{ $jumlahItemTidakAktif }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Senarai komponen --}}
            <div class="d-flex flex-column gap-3 mb-4">
                @forelse ($komponen as $item)
                    @php
                        $bilAktif = count($item->itemAktif());
                        $bilTidakAktif = count($item->amc_items ?? []) - $bilAktif;
                        $aktif = $item->amc_status === 'ACTIVE';
                    @endphp

                    <div @class(['kad-komponen', 'tidak-aktif' => ! $aktif])>
                        <div class="kepala-komponen">
                            <span class="nombor-komponen">{{ $item->amc_order }}</span>

                            <div class="flex-grow-1 min-w-0">
                                <h3 class="tajuk-komponen">{{ $item->amc_name }}</h3>
                                <p class="meta-komponen">
                                    {{ $bilAktif }} item aktif
                                    @if ($bilTidakAktif > 0)
                                        · {{ $bilTidakAktif }} tidak aktif
                                    @endif
                                    @if ($item->updated_at)
                                        · Dikemas kini {{ $item->updated_at->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>

                            <span class="lencana-jadual {{ $aktif ? 'utama' : 'kelabu' }}">
                                {{ \App\Support\Paparan::label($item->amc_status) }}
                            </span>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#modalKomponen{{ $item->amc_id }}"
                            >
                                <i class="bi bi-pencil-square me-1"></i>Ubah
                            </button>
                        </div>

                        <div class="isi-komponen">
                            @forelse ($item->amc_items ?? [] as $baris)
                                @php
                                    $itemAktif = ($baris['status'] ?? 'ACTIVE') === 'ACTIVE';
                                @endphp

                                <span
                                    @class(['cip-item', 'tidak-aktif' => ! $itemAktif])
                                    title="{{ $itemAktif ? 'Aktif' : 'Tidak aktif' }}"
                                >
                                    <i class="bi {{ $itemAktif ? 'bi-check-circle-fill' : 'bi-dash-circle' }}"></i>
                                    {{ $baris['nama'] }}
                                </span>
                            @empty
                                <small class="text-secondary">Tiada item.</small>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="kad-kosong">
                        <div class="ilustrasi-kosong">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h3>Tiada komponen penyelenggaraan</h3>
                        <p>Klik butang Tambah Komponen untuk mendaftarkan komponen pertama.</p>
                    </div>
                @endforelse
            </div>

            <div class="makluman-tetapan">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Komponen dan item tidak boleh dihapus supaya rekod pemeriksaan lama kekal lengkap.
                Matikan suis <strong>Aktif</strong> untuk menyembunyikannya daripada borang pemeriksaan baru.
            </div>
        </div>
    </div>

    {{-- Modal tambah --}}
    @include('tetapan._modal-komponen', [
        'idModal' => 'modalKomponenBaru',
        'tajuk' => 'Tambah Komponen',
        'action' => route('tetapan.komponen.simpan'),
        'method' => 'POST',
        'komponen' => null,
        'turutanLalai' => $turutanSeterusnya,
    ])

    {{-- Modal ubah (satu bagi setiap komponen) --}}
    @foreach ($komponen as $item)
        @include('tetapan._modal-komponen', [
            'idModal' => 'modalKomponen' . $item->amc_id,
            'tajuk' => 'Ubah Komponen',
            'action' => route('tetapan.komponen.kemaskini', $item),
            'method' => 'PUT',
            'komponen' => $item,
            'turutanLalai' => $item->amc_order,
        ])
    @endforeach

    {{-- Templat baris item baru --}}
    <template id="templatItemKomponen">
        <div class="baris-item-komponen" data-baris-item>
            <input type="hidden" name="items[__INDEX__][id]" value="">
            <input type="hidden" name="items[__INDEX__][turutan]" value="1" data-turutan>

            <span class="nombor-item" data-nombor>1</span>

            <input
                type="text"
                name="items[__INDEX__][nama]"
                class="form-control form-control-sm"
                placeholder="Nama item"
                maxlength="255"
                aria-label="Nama item"
                required
            >

            <input type="hidden" name="items[__INDEX__][status]" value="INACTIVE">
            <div class="form-check form-switch mb-0" title="Aktif / Tidak aktif">
                <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    name="items[__INDEX__][status]"
                    value="ACTIVE"
                    aria-label="Item aktif"
                    checked
                >
            </div>

            <button type="button" class="butang-ikon-kecil" data-naik title="Naik">
                <i class="bi bi-arrow-up"></i>
            </button>
            <button type="button" class="butang-ikon-kecil" data-turun title="Turun">
                <i class="bi bi-arrow-down"></i>
            </button>
            <button type="button" class="butang-ikon-kecil bahaya" data-buang-item title="Buang">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </template>
@endsection

@push('skrip')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const templat = document.getElementById('templatItemKomponen');

            // Nombor semula item dan turutan tersembunyi
            const susunSemula = (senarai) => {
                senarai.querySelectorAll('[data-baris-item]').forEach((baris, indeks) => {
                    baris.querySelector('[data-turutan]').value = indeks + 1;
                    baris.querySelector('[data-nombor]').textContent = indeks + 1;
                });
            };

            document.addEventListener('click', (event) => {
                const butangTambah = event.target.closest('[data-tambah-item]');

                if (butangTambah) {
                    const senarai = butangTambah
                        .closest('[data-borang-komponen]')
                        .querySelector('[data-senarai-item]');

                    const index = 'baru' + Date.now() + Math.floor(Math.random() * 1000);

                    senarai.insertAdjacentHTML(
                        'beforeend',
                        templat.innerHTML.replaceAll('__INDEX__', index)
                    );

                    susunSemula(senarai);
                    senarai.lastElementChild.querySelector('input[type="text"]').focus();

                    return;
                }

                const baris = event.target.closest('[data-baris-item]');

                if (! baris) {
                    return;
                }

                const senarai = baris.parentElement;

                if (event.target.closest('[data-naik]') && baris.previousElementSibling) {
                    senarai.insertBefore(baris, baris.previousElementSibling);
                    susunSemula(senarai);
                }

                if (event.target.closest('[data-turun]') && baris.nextElementSibling) {
                    baris.nextElementSibling.after(baris);
                    susunSemula(senarai);
                }

                if (event.target.closest('[data-buang-item]')) {
                    baris.remove();
                    susunSemula(senarai);
                }
            });

            // Buka semula modal yang gagal disahkan
            @if ($errors->any() && old('_modal'))
                const modalRalat = document.getElementById(@json(old('_modal')));

                if (modalRalat) {
                    bootstrap.Modal.getOrCreateInstance(modalRalat).show();
                }
            @endif
        });
    </script>
@endpush