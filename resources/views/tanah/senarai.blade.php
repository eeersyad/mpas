@extends('layouts.utama')

@section('tajuk', 'Aset Tanah')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Aset Tanah</h1>
            <p class="teks-penerangan mb-0">
                Senarai aset tanah yang telah didaftarkan.
            </p>
        </div>

        <a href="{{ route('tanah.create') }}" class="btn btn-primary px-3 py-2">
            <i class="bi bi-plus-circle-fill me-2"></i>Daftar Aset Tanah
        </a>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4"
        data-penapis-automatik
    >
        <input type="hidden" name="had" value="{{ request('had', 15) }}">

        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="cariTanah" class="label-penapis">
                    <i class="bi bi-search"></i> Carian Aset
                </label>

                <input
                    type="search"
                    id="cariTanah"
                    name="cari"
                    value="{{ request('cari') }}"
                    class="form-control kawalan-penapis"
                    placeholder="No. rujukan, lot, lokasi atau hakmilik..."
                >
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label for="statusHakmilik" class="label-penapis">
                    <i class="bi bi-file-earmark-text"></i> Status Hakmilik
                </label>

                <select
                    id="statusHakmilik"
                    name="status_hakmilik"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>

                    @foreach (['HAKMILIK', 'RIZAB', 'STRATA', 'LAIN_LAIN'] as $status)
                        <option value="{{ $status }}" @selected(request('status_hakmilik') === $status)>
                            {{ \App\Support\Paparan::label($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label for="statusTanah" class="label-penapis">
                    <i class="bi bi-key"></i> Status Tanah
                </label>

                <select
                    id="statusTanah"
                    name="status_tanah"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>

                    @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK'] as $status)
                        <option value="{{ $status }}" @selected(request('status_tanah') === $status)>
                            {{ \App\Support\Paparan::label($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-xl-4">
                <div class="d-flex gap-2 tindakan-penapis">
                    <a
                        href="{{ route('tanah.index') }}"
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
                        <th><x-susun medan="rujukan" label="No. Rujukan" /></th>
                        <th><x-susun medan="lot" label="No. Lot" /></th>
                        <th><x-susun medan="no_fail" label="No. Fail" /></th>
                        <th><x-susun medan="lokasi" label="Lokasi" /></th>
                        <th><x-susun medan="keluasan" label="Keluasan" /></th>
                        <th><x-susun medan="status_hakmilik" label="Status Hakmilik" /></th>
                        <th><x-susun medan="status_tanah" label="Status Tanah" /></th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tanah as $item)
                        <tr
                            class="baris-boleh-klik"
                            data-baris-butiran
                            data-url="{{ route('tanah.butiran', $item) }}"
                            tabindex="0"
                            role="button"
                            aria-label="Lihat butiran {{ $item->asl_lot_no }}"
                        >
                            <td>{{ $tanah->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $item->asl_ref_no }}</td>
                            <td>{{ $item->asl_lot_no }}</td>
                            <td>{{ $item->asl_no_file ?: '-' }}</td>
                            <td>{{ $item->asl_location }}</td>
                            <td>{{ number_format($item->asl_land_size, 2) }} m²</td>
                            <td>
                                <span class="lencana-jadual biru">
                                    {{ \App\Support\Paparan::label($item->asl_status_hakmilik) }}
                                </span>
                            </td>

                            @php
                                $warnaStatusTanah = match ($item->asl_status_tanah) {
                                    'SEWA' => 'jenis-sewa',
                                    'TIDAK_DISEWA' => 'jenis-tidak-disewa',
                                    'PAJAK' => 'jenis-pajak',
                                    'TIDAK_DIPAJAK' => 'jenis-tidak-dipajak',
                                    default => 'kelabu',
                                };
                            @endphp

                            <td>
                                <span class="lencana-jadual {{ $warnaStatusTanah }}">
                                    {{ \App\Support\Paparan::label($item->asl_status_tanah) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a
                                        href="{{ route('tanah.pdf', $item) }}"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Muat turun maklumat aset tanah dalam PDF"
                                        aria-label="Muat turun PDF aset tanah"
                                    >
                                        <i class="bi bi-download"></i>
                                    </a>

                                    <a
                                        href="{{ route('tanah.edit', $item) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Kemaskini aset tanah"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center">
                                <i class="bi bi-map text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">Tiada aset tanah didaftarkan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-jadual :paginator="$tanah" />
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