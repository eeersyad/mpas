@extends('layouts.utama')

@section('tajuk', 'Aduan')

@section('kandungan')
    <div class="mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN ADUAN</p>
        <h1 class="tajuk-halaman mb-2">Aduan Aset Bangunan</h1>
        <p class="teks-penerangan mb-0">
            Senarai aduan kerosakan aset bangunan.
        </p>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4"
        data-penapis-automatik
    >
        <input type="hidden" name="had" value="{{ request('had', 15) }}">

        <div class="row g-3 align-items-end">
            <div class="col-12 col-xl-4">
                <label for="cariAduan" class="label-penapis">
                    <i class="bi bi-search"></i> Carian Aduan
                </label>

                <input
                    type="search"
                    id="cariAduan"
                    name="cari"
                    value="{{ request('cari') }}"
                    class="form-control kawalan-penapis"
                    placeholder="Aset, rujukan, kerosakan atau catatan..."
                >
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label for="statusAduan" class="label-penapis">
                    <i class="bi bi-clipboard-check"></i> Status
                </label>

                <select
                    id="statusAduan"
                    name="status"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>

                    @foreach (['BARU', 'DITERIMA', 'DALAM_PENYELENGGARAAN', 'SELESAI'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ \App\Support\Paparan::label($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label for="jenisPengadu" class="label-penapis">
                    <i class="bi bi-people"></i> Pengadu
                </label>

                <select
                    id="jenisPengadu"
                    name="jenis_pengadu"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Pengadu</option>

                    <option value="PENYEWA" @selected(request('jenis_pengadu') === 'PENYEWA')>
                        Penyewa
                    </option>

                    <option value="AWAM" @selected(request('jenis_pengadu') === 'AWAM')>
                        Orang Awam
                    </option>
                </select>
            </div>

            <div class="col-12 col-xl-4">
                <div class="d-flex gap-2 tindakan-penapis">
                    <a
                        href="{{ route('aduan.index') }}"
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
                        <th>Tarikh Aduan</th>
                        <th>Aset Bangunan</th>
                        <th>Jenis Kerosakan</th>
                        <th>Status</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($aduan as $item)
                        @php
                            $warnaStatus = match ($item->cmp_status) {
                                'BARU' => 'text-bg-primary',
                                'DITERIMA' => 'text-bg-info',
                                'DALAM_PENYELENGGARAAN' => 'text-bg-warning',
                                'SELESAI' => 'text-bg-success',
                                default => 'text-bg-secondary',
                            };
                        @endphp

                        <tr>
                            <td>{{ $aduan->firstItem() + $loop->index }}</td>

                            <td>
                                {{ $item->cmp_received_date?->format('d/m/Y') }}
                                <small class="d-block text-secondary">
                                    {{ $item->cmp_received_date?->format('h:i A') }}
                                </small>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $item->bangunan?->asb_name ?? '-' }}
                                </div>
                                <small class="text-secondary">
                                    {{ $item->bangunan?->asb_ref_no ?? '-' }}
                                </small>
                            </td>

                            <td>
                                {{ \App\Support\Paparan::label($item->cmp_damage_type) }}
                            </td>

                            <td>
                                <span class="badge {{ $warnaStatus }}">
                                    {{ \App\Support\Paparan::label($item->cmp_status) }}
                                </span>
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('aduan.show', $item) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Lihat butiran aduan"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <i class="bi bi-clipboard2-x text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">
                                    Tiada aduan direkodkan buat masa ini.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-jadual :paginator="$aduan" />
    </div>
@endsection