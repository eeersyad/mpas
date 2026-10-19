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

    <form method="GET" class="panel-penapis mb-4">
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
                <label for="jenisHakmilik" class="label-penapis">
                    <i class="bi bi-key"></i> Jenis Hakmilik
                </label>

                <select
                    id="jenisHakmilik"
                    name="jenis_hakmilik"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Jenis</option>

                    @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEKAL'] as $jenis)
                        <option value="{{ $jenis }}" @selected(request('jenis_hakmilik') === $jenis)>
                            {{ \App\Support\Paparan::label($jenis) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-xl-4">
                <div class="d-flex gap-2 tindakan-penapis">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>

                    <a
                        href="{{ route('tanah.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Tetapkan Semula
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
                        <th>No. Rujukan</th>
                        <th>No. Lot</th>
                        <th>Lokasi</th>
                        <th>Keluasan</th>
                        <th>Status Hakmilik</th>
                        <th>Jenis Hakmilik</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tanah as $item)
                        <tr>
                            <td>{{ $tanah->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $item->asl_ref_no }}</td>
                            <td>{{ $item->asl_lot_no }}</td>
                            <td>{{ $item->asl_location }}</td>
                            <td>{{ number_format($item->asl_land_size, 2) }}</td>
                            <td>
                                <span class="lencana-jadual biru">
                                    {{ \App\Support\Paparan::label($item->asl_status_hakmilik) }}
                                </span>
                            </td>

                            @php
                                $warnaJenisHakmilik = match ($item->asl_hakmilik_type) {
                                    'SEWA' => 'jenis-sewa',
                                    'TIDAK_DISEWA' => 'jenis-tidak-disewa',
                                    'PAJAK' => 'jenis-pajak',
                                    'TIDAK_DIPAJAK' => 'jenis-tidak-dipajak',
                                    'KEKAL' => 'jenis-kekal',
                                    default => 'kelabu',
                                };
                            @endphp

                            <td>
                                <span class="lencana-jadual {{ $warnaJenisHakmilik }}">
                                    {{ \App\Support\Paparan::label($item->asl_hakmilik_type) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-buka-butiran
                                        data-url="{{ route('tanah.butiran', $item) }}"
                                        title="Lihat butiran aset tanah"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>

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
                            <td colspan="8" class="py-5 text-center">
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