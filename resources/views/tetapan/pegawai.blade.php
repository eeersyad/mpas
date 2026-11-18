@extends('layouts.utama')

@section('tajuk', 'Tetapan Pegawai Aset')

@section('kandungan')
    <div class="hero-tetapan mb-4 d-none d-md-block">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
        <p class="teks-penerangan mb-0 d-none d-md-block">Urus konfigurasi dan akses sistem.</p>
    </div>

    @include('tetapan._bar-apl', ['tajuk' => 'Pegawai Aset'])

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
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <div class="min-w-0">
                                <h2 class="h6 fw-bold mb-1">Pegawai Aset</h2>
                                <p class="mb-0 text-secondary small">
                                    Senarai pegawai pengumpul dan pengesah yang dipaparkan dalam borang aset bangunan.
                                </p>
                            </div>
                        </div>

                        <button type="button" class="btn btn-utama butang-tambah-ikon flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalPegawaiBaru" title="Tambah Pegawai" aria-label="Tambah Pegawai">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Senarai --}}
            {{-- ===================== TELEFON: senarai pegawai ===================== --}}
            @php
                [$pegawaiAktif, $pegawaiTidakAktif] = $pegawai->partition(fn ($p) => $p->aktif());
            @endphp

            <div class="d-md-none tetapan-apl">
                @if ($pegawai->isEmpty())
                    <div class="kad-kosong">
                        <div class="ilustrasi-kosong"><i class="bi bi-person-badge"></i></div>
                        <h3>Tiada pegawai</h3>
                        <p>Tekan butang + untuk tambah pegawai pertama.</p>
                    </div>
                @endif

                @if ($pegawaiAktif->isNotEmpty())
                    <p class="label-kumpulan-apl">Aktif · {{ $pegawaiAktif->count() }}</p>

                    <div class="kad-senarai-apl mb-3">
                        @foreach ($pegawaiAktif as $item)
                            @include('tetapan._baris-pegawai', ['item' => $item])
                        @endforeach
                    </div>
                @endif

                @if ($pegawaiTidakAktif->isNotEmpty())
                    <button type="button" class="label-kumpulan-apl butang-lipat-apl collapsed" data-bs-toggle="collapse" data-bs-target="#pegawaiTidakAktif" aria-expanded="false" aria-controls="pegawaiTidakAktif">
                        Tidak aktif · {{ $pegawaiTidakAktif->count() }}
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>

                    <div class="collapse" id="pegawaiTidakAktif">
                        <div class="kad-senarai-apl mb-3 senarai-tidak-aktif-apl">
                            @foreach ($pegawaiTidakAktif as $item)
                                @include('tetapan._baris-pegawai', ['item' => $item])
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="nota-kumpulan-apl">
                    Tekan pegawai untuk ubah maklumat atau status. Pegawai tidak boleh dihapus;
                    matikan suis Aktif untuk sembunyikan daripada borang aset bangunan.
                </p>
            </div>

            <div class="kad-data mb-4 d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 jadual-sistem jadual-pegawai" data-kad-apl="sembunyi|tajuk|sub|lencana|lencana|tindakan">
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>Nama Pegawai</th>
                                <th>Jawatan</th>
                                <th>Peranan</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($pegawai as $item)
                                <tr @class(['opacity-75' => ! $item->aktif()])>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->pgw_nama }}</td>
                                    <td>{{ $item->pgw_jawatan }}</td>
                                    <td>
                                        <span class="lencana-jadual biru">
                                            {{ \App\Support\Paparan::label($item->pgw_peranan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-switch suis-status-tetapan mb-0" title="Aktif / Tidak aktif">
                                                <input class="form-check-input" type="checkbox" role="switch" data-togol-status="{{ route('tetapan.pegawai.status', $item) }}" data-modal-sasaran="#modalPegawai{{ $item->pgw_id }}" aria-label="Pegawai {{ $item->pgw_nama }} aktif" @checked($item->aktif())>
                                            </div>

                                            <span class="lencana-jadual {{ $item->aktif() ? 'utama' : 'kelabu' }}" data-lencana-status>
                                                {{ \App\Support\Paparan::label($item->pgw_status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalPegawai{{ $item->pgw_id }}" aria-label="Ubah pegawai {{ $item->pgw_nama }}">
                                            <i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline ms-1">Ubah</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center">
                                        <i class="bi bi-person-x text-secondary fs-2 d-block mb-2"></i>
                                        <span class="text-secondary">Tiada pegawai didaftarkan. Klik butang + untuk menambah pegawai.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="makluman-tetapan d-none d-md-block">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Pegawai tidak boleh dihapus supaya rekod aset lama kekal lengkap.
                Matikan suis <strong>Aktif</strong> untuk menyembunyikannya daripada borang aset bangunan.
            </div>
        </div>
    </div>

    {{-- Butang tambah terapung (telefon) --}}
    <button type="button" class="fab-tambah d-md-none" data-bs-toggle="modal" data-bs-target="#modalPegawaiBaru" aria-label="Tambah Pegawai" title="Tambah Pegawai">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
    </button>

    <div class="ruang-fab d-md-none" aria-hidden="true"></div>

    {{-- Modal tambah --}}
    @include('tetapan._modal-pegawai', [
        'idModal' => 'modalPegawaiBaru',
        'tajuk' => 'Tambah Pegawai',
        'action' => route('tetapan.pegawai.simpan'),
        'method' => 'POST',
        'pegawai' => null,
    ])

    {{-- Modal ubah --}}
    @foreach ($pegawai as $item)
        @include('tetapan._modal-pegawai', [
            'idModal' => 'modalPegawai' . $item->pgw_id,
            'tajuk' => 'Ubah Pegawai',
            'action' => route('tetapan.pegawai.kemaskini', $item),
            'method' => 'PUT',
            'pegawai' => $item,
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
