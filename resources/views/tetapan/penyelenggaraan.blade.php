@extends('layouts.utama')

@section('tajuk', 'Tetapan Akses Penyelenggaraan')

@section('kandungan')
    <div class="hero-tetapan mb-4 d-none d-md-block">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
        <p class="teks-penerangan mb-0 d-none d-md-block">Urus konfigurasi dan akses sistem.</p>
    </div>

    @include('tetapan._bar-apl', ['tajuk' => 'Akses Penyelenggaraan'])

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3 d-none d-md-block">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            {{-- Kepala --}}
            <div class="kad-data kad-tetapan mb-4 d-none d-md-block">
                <div class="kepala-kad-tetapan">
                    <div class="d-flex align-items-center justify-content-between gap-3 w-100">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <div class="min-w-0">
                                <h2 class="h6 fw-bold mb-1">Akses Penyelenggaraan</h2>
                                <p class="mb-0 text-secondary small">
                                    Pengguna yang boleh melihat dan mengisi rekod pemeriksaan penyelenggaraan aset bangunan.
                                </p>
                            </div>
                        </div>

                        <button type="button" class="btn btn-utama butang-tambah-ikon flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalAksesBaru" title="Tambah Pengguna" aria-label="Tambah Pengguna">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ===================== TELEFON: senarai pengguna ===================== --}}
            @php
                [$aksesAktif, $aksesTidakAktif] = $akses->partition(fn ($a) => $a->aktif());
            @endphp

            <div class="d-md-none tetapan-apl">
                <p class="label-kumpulan-apl">Sentiasa ada akses</p>

                <div class="kad-senarai-apl mb-3">
                    <div class="baris-tetapan-medan baris-pegawai-apl">
                        <span class="avatar-apl">SYS</span>
                        <span class="teks-baris-laporan">
                            <strong>SYSTEM <i class="bi bi-lock-fill text-secondary small"></i></strong>
                            <small>Semua pengguna Super Admin</small>
                        </span>
                        <span class="lencana-peranan-apl">Lihat &amp; Isi</span>
                    </div>
                </div>

                @if ($akses->isEmpty())
                    <div class="kad-kosong">
                        <div class="ilustrasi-kosong"><i class="bi bi-shield-lock"></i></div>
                        <h3>Tiada pengguna</h3>
                        <p>Tekan butang + untuk beri akses kepada pengguna.</p>
                    </div>
                @endif

                @if ($aksesAktif->isNotEmpty())
                    <p class="label-kumpulan-apl">Aktif · {{ $aksesAktif->count() }}</p>

                    <div class="kad-senarai-apl mb-3">
                        @foreach ($aksesAktif as $item)
                            @include('tetapan._baris-akses', ['item' => $item])
                        @endforeach
                    </div>
                @endif

                @if ($aksesTidakAktif->isNotEmpty())
                    <button type="button" class="label-kumpulan-apl butang-lipat-apl collapsed" data-bs-toggle="collapse" data-bs-target="#aksesTidakAktif" aria-expanded="false" aria-controls="aksesTidakAktif">
                        Tidak aktif · {{ $aksesTidakAktif->count() }}
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>

                    <div class="collapse" id="aksesTidakAktif">
                        <div class="kad-senarai-apl mb-3 senarai-tidak-aktif-apl">
                            @foreach ($aksesTidakAktif as $item)
                                @include('tetapan._baris-akses', ['item' => $item])
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="nota-kumpulan-apl">
                    Pengguna baharu tidak mendapat akses secara automatik. Tekan pengguna untuk ubah tahap akses atau status.
                    Rekod pemeriksaan tidak boleh dihapus oleh sesiapa.
                </p>
            </div>

            {{-- ===================== DESKTOP: jadual ===================== --}}
            <div class="kad-data mb-4 d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 jadual-sistem jadual-pegawai" data-tanpa-kad>
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>Pengguna</th>
                                <th>Peranan</th>
                                <th>Tahap Akses</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="table-light">
                                <td><i class="bi bi-lock-fill text-secondary"></i></td>
                                <td class="fw-semibold">SYSTEM <small class="text-secondary fw-normal">· semua Super Admin</small></td>
                                <td><span class="lencana-jadual kelabu">SYSTEM</span></td>
                                <td><span class="lencana-jadual utama">Lihat &amp; Isi</span></td>
                                <td><span class="text-secondary small">Sentiasa ada akses</span></td>
                                <td></td>
                            </tr>

                            @forelse ($akses as $item)
                                <tr @class(['opacity-75' => ! $item->aktif()])>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->pengguna->usr_name }}</td>
                                    <td><span class="lencana-jadual kelabu">{{ $item->pengguna->usr_level }}</span></td>
                                    <td>
                                        <span class="lencana-jadual {{ $item->bolehIsi() ? 'utama' : 'biru' }}">{{ $item->labelTahap() }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-switch suis-status-tetapan mb-0" title="Aktif / Tidak aktif">
                                                <input class="form-check-input" type="checkbox" role="switch" data-togol-status="{{ route('tetapan.penyelenggaraan.status', $item) }}" data-modal-sasaran="#modalAkses{{ $item->tap_id }}" aria-label="Akses {{ $item->pengguna->usr_name }} aktif" @checked($item->aktif())>
                                            </div>

                                            <span class="lencana-jadual {{ $item->aktif() ? 'utama' : 'kelabu' }}" data-lencana-status>
                                                {{ \App\Support\Paparan::label($item->tap_status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAkses{{ $item->tap_id }}" aria-label="Ubah akses {{ $item->pengguna->usr_name }}">
                                            <i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline ms-1">Ubah</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center">
                                        <i class="bi bi-shield-x text-secondary fs-2 d-block mb-2"></i>
                                        <span class="text-secondary">Tiada pengguna diberi akses. Klik butang + untuk menambah pengguna.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="makluman-tetapan d-none d-md-block">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Pengguna baharu <strong>tidak</strong> mendapat akses secara automatik; tambah mereka di sini jika perlu.
                <strong>Lihat &amp; Isi</strong> termasuk akses lihat. Rekod pemeriksaan tidak boleh dihapus oleh sesiapa.
            </div>
        </div>
    </div>

    {{-- Butang tambah terapung (telefon) --}}
    <button type="button" class="fab-tambah d-md-none" data-bs-toggle="modal" data-bs-target="#modalAksesBaru" aria-label="Tambah Pengguna" title="Tambah Pengguna">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
    </button>

    <div class="ruang-fab d-md-none" aria-hidden="true"></div>

    {{-- Modal tambah --}}
    @include('tetapan._modal-akses', [
        'idModal' => 'modalAksesBaru',
        'tajuk' => 'Tambah Pengguna',
        'action' => route('tetapan.penyelenggaraan.simpan'),
        'method' => 'POST',
        'akses' => null,
    ])

    {{-- Modal ubah --}}
    @foreach ($akses as $item)
        @include('tetapan._modal-akses', [
            'idModal' => 'modalAkses' . $item->tap_id,
            'tajuk' => 'Ubah Akses',
            'action' => route('tetapan.penyelenggaraan.kemaskini', $item),
            'method' => 'PUT',
            'akses' => $item,
        ])
    @endforeach
@endsection

@push('skrip')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
