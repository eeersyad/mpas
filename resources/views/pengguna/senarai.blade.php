@extends('layouts.utama')

@section('tajuk', 'Pengurusan Pengguna')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
            <h1 class="tajuk-halaman mb-2">Pengguna</h1>
            <p class="teks-penerangan mb-0">
                Urus rekod pengguna dan akses sistem.
            </p>
        </div>

        <a href="{{ route('pengguna.create') }}" class="btn btn-primary px-3 py-2">
            <i class="bi bi-person-plus-fill me-2"></i>Daftar Pengguna
        </a>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4"
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

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 jadual-sistem">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>Nama Pengguna</th>
                        <th>Peranan</th>
                        <th>Status</th>
                        <th>Tarikh Daftar</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengguna as $item)
                        <tr>
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
                                <div class="d-inline-flex gap-2">
                                    <a
                                        href="{{ route('pengguna.edit', $item) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Kemaskini pengguna"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('pengguna.destroy', $item) }}"
                                        class="d-inline"
                                        data-padam-pengguna
                                        data-nama-pengguna="{{ $item->usr_name }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Padam pengguna"
                                        >
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
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
@endsection