@extends('layouts.utama')

@section('tajuk', 'Tetapan Pegawai Aset')

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
            {{-- Kepala --}}
            <div class="kad-data kad-tetapan mb-4">
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
            <div class="kad-data mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 jadual-sistem">
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
                                        <span class="lencana-jadual {{ $item->aktif() ? 'utama' : 'kelabu' }}">
                                            {{ \App\Support\Paparan::label($item->pgw_status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalPegawai{{ $item->pgw_id }}">
                                            <i class="bi bi-pencil-square me-1"></i>Ubah
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

            <div class="makluman-tetapan">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Pegawai tidak boleh dihapus supaya rekod aset lama kekal lengkap.
                Matikan suis <strong>Aktif</strong> untuk menyembunyikannya daripada borang aset bangunan.
            </div>
        </div>
    </div>

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
