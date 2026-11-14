@extends('layouts.utama')

@section('tajuk', 'Pengurusan Pengguna')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1 d-none d-md-block">PENGURUSAN SISTEM</p>
            <h1 class="tajuk-halaman mb-2">Pengguna <span class="lencana-bilangan-tajuk d-md-none">{{ number_format($pengguna->total()) }}</span></h1>
            <p class="teks-penerangan mb-0 d-none d-md-block">
                Urus rekod pengguna dan akses sistem.
            </p>
        </div>

        <a href="{{ route('pengguna.create') }}" class="btn btn-primary px-3 py-2 d-none d-md-inline-block">
            <i class="bi bi-person-plus-fill me-2"></i>Daftar Pengguna
        </a>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4 d-none d-md-block"
        data-penapis-automatik
    >
        <input type="hidden" name="had" value="{{ request('had', 15) }}">

        <div class="row g-3 align-items-end">
            <div class="col-12 col-xl-5">
                <label for="cariPengguna" class="label-penapis">
                    <i class="bi bi-search"></i> Carian Pengguna
                </label>

                <input
                    type="search"
                    id="cariPengguna"
                    name="cari"
                    value="{{ request('cari') }}"
                    class="form-control kawalan-penapis"
                    placeholder="Nama pengguna..."
                >
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label for="perananPengguna" class="label-penapis">
                    <i class="bi bi-person-badge"></i> Peranan
                </label>

                <select
                    id="perananPengguna"
                    name="peranan"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Peranan</option>

                    @foreach (['BPH', 'BP', 'KJ'] as $peranan)
                        <option value="{{ $peranan }}" @selected(request('peranan') === $peranan)>
                            {{ $peranan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label for="statusPengguna" class="label-penapis">
                    <i class="bi bi-toggle-on"></i> Status Akaun
                </label>

                <select
                    id="statusPengguna"
                    name="status"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>
                    <option value="ACTIVE" @selected(request('status') === 'ACTIVE')>
                        Aktif
                    </option>
                    <option value="INACTIVE" @selected(request('status') === 'INACTIVE')>
                        Tidak Aktif
                    </option>
                </select>
            </div>

            <div class="col-12 col-xl-3">
                <div class="d-flex gap-2 tindakan-penapis">
                    <a
                        href="{{ route('pengguna.index') }}"
                        class="btn btn-outline-secondary"
                        title="Tetapkan semula penapis"
                        aria-label="Tetapkan semula penapis"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="d-xl-none">Semula</span>
                    </a>
                </div>
            </div>
        </div>
    </form>

    @php
        // Penapis untuk paparan telefon (bottom sheet)
        $penapisMobile = [
            'peranan' => ['label' => 'Peranan', 'pilihan' => ['BPH' => 'BPH', 'BP' => 'BP', 'KJ' => 'KJ']],
            'status' => ['label' => 'Status Akaun', 'pilihan' => ['ACTIVE' => 'Aktif', 'INACTIVE' => 'Tidak Aktif']],
        ];
    @endphp

    <x-penapis-mobile placeholder="Cari nama pengguna..." :penapis="$penapisMobile" :susun="['nama' => 'Nama', 'peranan' => 'Peranan', 'status' => 'Status', 'tarikh' => 'Tarikh Daftar']" :url-semula="route('pengguna.index')" />

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 jadual-sistem" data-kad-apl="sembunyi|tajuk|sub|lencana|info=Daftar|tindakan">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th><x-susun medan="nama" label="Nama Pengguna" /></th>
                        <th><x-susun medan="peranan" label="Peranan" /></th>
                        <th><x-susun medan="status" label="Status" /></th>
                        <th><x-susun medan="tarikh" label="Tarikh Daftar" /></th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengguna as $item)
                        <tr
                            class="baris-boleh-klik"
                            data-baris-butiran-pengguna
                            tabindex="0"
                            role="button"
                            aria-label="Lihat butiran pengguna {{ $item->usr_name }}"
                        >
                            <td>{{ $pengguna->firstItem() + $loop->index }}</td>

                            <td>
                                <div class="fw-semibold">{{ $item->usr_name }}</div>
                            </td>

                            <td>
                                <span class="lencana-peranan {{ strtolower($item->usr_level) }}">
                                    {{ $item->usr_level }}
                                </span>
                            </td>

                            <td>
                                @if ($item->usr_status === 'ACTIVE')
                                    <span class="lencana-status aktif">
                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                    </span>
                                @else
                                    <span class="lencana-status tidak-aktif">
                                        <i class="bi bi-x-circle-fill"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $item->created_at?->format('d/m/Y, h:i A') ?? '-' }}
                            </td>

                            <td class="text-end">
                                {{-- Menu titik tiga (telefon) --}}
                                <div class="dropdown d-md-none">
                                    <button type="button" class="btn butang-menu-kad" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilihan tindakan">
                                        <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end menu-kad">
                                        <li><button type="button" class="dropdown-item" data-buka-butiran-kad><i class="bi bi-eye" aria-hidden="true"></i>Lihat butiran</button></li>
                                        <li><a class="dropdown-item" href="{{ route('pengguna.edit', $item) }}"><i class="bi bi-pencil-square" aria-hidden="true"></i>Kemaskini</a></li>
                                    </ul>
                                </div>

                                <div class="d-none d-md-inline-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-buka-butiran-pengguna
                                        data-nama="{{ $item->usr_name }}"
                                        data-peranan="{{ $item->usr_level }}"
                                        data-status="{{ $item->usr_status === 'ACTIVE' ? 'Aktif' : 'Tidak Aktif' }}"
                                        data-tarikh="{{ $item->created_at?->format('d/m/Y, h:i A') ?? '-' }}"
                                        data-nama-peranan="{{ $namaPeranan[$item->usr_level] ?? $item->usr_level }}"
                                        data-url-kemaskini="{{ route('pengguna.edit', $item) }}"
                                        title="Lihat butiran pengguna"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a
                                        href="{{ route('pengguna.edit', $item) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Kemaskini pengguna"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <i class="bi bi-people text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">Tiada rekod pengguna ditemui.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-jadual :paginator="$pengguna" />
    </div>
    <div
        class="modal fade"
        id="modalButiranPengguna"
        tabindex="-1"
        aria-labelledby="tajukModalButiranPengguna"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    {{-- Bar aplikasi (telefon) --}}
                    <div class="bar-apl-butiran d-md-none">
                        <button type="button" class="butang-bar-apl" data-bs-dismiss="modal" aria-label="Kembali">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        </button>

                        <span class="tajuk-bar-apl">Butiran Pengguna</span>

                        <div class="dropdown">
                            <button type="button" class="butang-bar-apl" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilihan lain">
                                <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end menu-kad">
                                <li><a class="dropdown-item" href="#" data-pautan-kemaskini-pengguna><i class="bi bi-pencil-square" aria-hidden="true"></i>Kemaskini</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-none d-md-block">
                        <p class="teks-kecil mb-1">MAKLUMAT PENGGUNA</p>
                        <h5 class="modal-title" id="tajukModalButiranPengguna">
                            Butiran Pengguna
                        </h5>
                    </div>

                    <button
                        type="button"
                        class="btn-close d-none d-md-block"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="modal-body" data-kandungan-butiran-pengguna></div>

                {{-- Bar tindakan (telefon) --}}
                <div class="modal-footer d-md-none">
                    <a href="#" class="btn btn-primary butang-kemaskini-apl" data-pautan-kemaskini-pengguna>
                        <i class="bi bi-pencil-square me-1"></i>Kemaskini Pengguna
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- Butang tambah terapung (telefon) --}}
    <a href="{{ route('pengguna.create') }}" class="fab-tambah d-md-none" aria-label="Daftar Pengguna" title="Daftar Pengguna">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
    </a>

    <div class="ruang-fab d-md-none" aria-hidden="true"></div>
@endsection