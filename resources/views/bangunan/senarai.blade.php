@extends('layouts.utama')

@section('tajuk', 'Aset Bangunan')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Aset Bangunan</h1>
            <p class="teks-penerangan mb-0">
                Senarai aset bangunan yang telah didaftarkan.
            </p>
        </div>

        <a href="{{ route('bangunan.create') }}" class="btn btn-primary px-3 py-2">
            <i class="bi bi-plus-circle-fill me-2"></i>Daftar Aset Bangunan
        </a>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4"
        data-penapis-automatik
    >
        <input type="hidden" name="had" value="{{ request('had', 15) }}">

        <div class="panel-penapis-kepala">
            <div class="panel-penapis-ikon">
                <i class="bi bi-sliders2"></i>
            </div>

            <div>
                <h2 class="panel-penapis-tajuk">Carian dan Penapis</h2>
                <p class="panel-penapis-penerangan mb-0">
                    Carian aset mengikut rujukan, status premis atau tahap penyelenggaraan.
                </p>
            </div>
        </div>

        <div class="row g-3 align-items-end">
            <div class="col-12 col-xl-4">
                <label for="cariBangunan" class="label-penapis">
                    <i class="bi bi-search"></i> Carian Aset
                </label>

                <input
                    type="search"
                    id="cariBangunan"
                    name="cari"
                    value="{{ request('cari') }}"
                    class="form-control kawalan-penapis"
                    placeholder="No. rujukan, nama, bandar atau kategori..."
                >
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <label for="statusPremis" class="label-penapis">
                    <i class="bi bi-building-check"></i> Status Premis
                </label>

                <select
                    id="statusPremis"
                    name="status_premis"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>

                    @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEGUNAAN_MDM', 'DILUPUS'] as $status)
                        <option value="{{ $status }}" @selected(request('status_premis') === $status)>
                            {{ \App\Support\Paparan::label($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <label for="statusPenyelenggaraan" class="label-penapis">
                    <i class="bi bi-tools"></i> Penyelenggaraan
                </label>

                <select
                    id="statusPenyelenggaraan"
                    name="status_penyelenggaraan"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>

                    @foreach (['RENDAH', 'SEDERHANA', 'KRITIKAL'] as $status)
                        <option value="{{ $status }}" @selected(request('status_penyelenggaraan') === $status)>
                            {{ \App\Support\Paparan::label($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-xl-2">
                <div class="d-flex gap-2 tindakan-penapis">
                    <a
                        href="{{ route('bangunan.index') }}"
                        class="btn btn-outline-secondary"
                        title="Tetapkan semula penapis"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="d-xl-none ms-1">Semula</span>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 jadual-sistem jadual-bangunan">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th><x-susun medan="rujukan" label="No. Rujukan" /></th>
                        <th><x-susun medan="nama" label="Nama Bangunan" /></th>
                        <th><x-susun medan="kategori" label="Kategori" /></th>
                        <th><x-susun medan="status_premis" label="Status Premis" /></th>
                        <th><x-susun medan="penyelenggaraan" label="Status Penyelenggaraan" /></th>
                        <th><x-susun medan="penyewa" label="Penyewa Aktif" /></th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($bangunan as $item)
                        <tr
                            class="baris-boleh-klik"
                            data-baris-butiran
                            tabindex="0"
                            role="button"
                            aria-label="Lihat butiran {{ $item->asb_name }}"
                        >
                            <td>{{ $bangunan->firstItem() + $loop->index }}</td>

                            <td class="fw-semibold">{{ $item->asb_ref_no }}</td>

                            <td>
                                <div class="fw-semibold">{{ $item->asb_name }}</div>
                                <small class="text-secondary">{{ $item->asb_city ?? '-' }}</small>
                            </td>

                            <td>
                                <span class="lencana-jadual kelabu">
                                    {{ \App\Support\Paparan::label($item->asb_category) }}
                                </span>
                            </td>

                            @php
                                $warnaPremis = match ($item->asb_premise_status) {
                                    'SEWA' => 'premis-sewa',
                                    'TIDAK_DISEWA' => 'premis-tidak-disewa',
                                    'PAJAK' => 'premis-pajak',
                                    'TIDAK_DIPAJAK' => 'premis-tidak-dipajak',
                                    'KEGUNAAN_MDM' => 'premis-kegunaan-mdm',
                                    'DILUPUS' => 'premis-dilupus',
                                    default => 'kelabu',
                                };
                            @endphp

                            <td>
                                <span class="lencana-jadual {{ $warnaPremis }}">
                                    {{ \App\Support\Paparan::label($item->asb_premise_status) }}
                                </span>
                            </td>

                            @php
                                $warnaPenyelenggaraan = match ($item->asb_maintenance_status) {
                                    'RENDAH' => 'penyelenggaraan-rendah',
                                    'SEDERHANA' => 'penyelenggaraan-sederhana',
                                    'KRITIKAL' => 'penyelenggaraan-kritikal',
                                    default => 'kelabu',
                                };
                            @endphp

                            <td>
                                <span class="lencana-jadual {{ $warnaPenyelenggaraan }}">
                                    {{ $item->asb_maintenance_status
                                        ? \App\Support\Paparan::label($item->asb_maintenance_status)
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="lencana-bilangan">
                                    <i class="bi bi-people"></i>
                                    {{ $item->penyewa_aktif_count }} penyewa
                                </span>
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-buka-butiran
                                        data-url="{{ route('bangunan.butiran', $item) }}"
                                        title="Lihat butiran aset bangunan"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a
                                        href="{{ route('bangunan.edit', $item) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Kemaskini aset bangunan"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center">
                                <i class="bi bi-buildings text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">Tiada aset bangunan didaftarkan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-jadual :paginator="$bangunan" />
    </div>
    <div
        class="modal fade"
        id="modalButiranAset"
        tabindex="-1"
        aria-labelledby="tajukModalButiranAset"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content border-0 shadow" data-kandungan-modal>
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-secondary mt-3 mb-0">Memuatkan butiran aset...</p>
                </div>
            </div>
        </div>
    </div>
@endsection