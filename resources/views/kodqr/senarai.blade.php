@extends('layouts.utama')

@section('tajuk', 'Kod QR')

@section('kandungan')
    <div class="mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
        <h1 class="tajuk-halaman mb-2">Kod QR Aset Bangunan</h1>
        <p class="teks-penerangan mb-0">
            Lihat dan urus status kod QR bagi setiap aset bangunan.
        </p>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4"
        data-penapis-automatik
    >
        <input type="hidden" name="had" value="{{ request('had', 15) }}">

        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-6">
                <label for="cariQr" class="label-penapis">
                    <i class="bi bi-search"></i> Carian Aset Bangunan
                </label>

                <input
                    type="search"
                    id="cariQr"
                    name="cari"
                    value="{{ request('cari') }}"
                    class="form-control kawalan-penapis"
                    placeholder="No. rujukan atau nama bangunan..."
                >
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label for="statusQr" class="label-penapis">
                    <i class="bi bi-qr-code"></i> Status Kod QR
                </label>

                <select
                    id="statusQr"
                    name="status_qr"
                    class="form-select kawalan-penapis"
                >
                    <option value="">Semua Status</option>

                    <option value="ACTIVE" @selected(request('status_qr') === 'ACTIVE')>
                        Aktif
                    </option>

                    <option value="INACTIVE" @selected(request('status_qr') === 'INACTIVE')>
                        Tidak Aktif
                    </option>
                </select>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="d-flex gap-2 tindakan-penapis">
                    <a
                        href="{{ route('kodqr.index') }}"
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
                        <th><x-susun medan="aset" label="Aset Bangunan" /></th>
                        <th><x-susun medan="status" label="Status QR" /></th>
                        <th><x-susun medan="tarikh" label="Tarikh Dijana" /></th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($bangunan as $item)
                        <tr
                            class="baris-boleh-klik"
                            data-baris-pautan
                            data-url="{{ route('kodqr.show', $item) }}"
                            tabindex="0"
                            role="link"
                            aria-label="Lihat kod QR {{ $item->asb_name }}"
                        >
                            <td>{{ $bangunan->firstItem() + $loop->index }}</td>

                            <td class="fw-semibold">{{ $item->asb_ref_no }}</td>

                            <td>
                                <div class="fw-semibold">{{ $item->asb_name }}</div>
                                <small class="text-secondary">
                                    {{ $item->asb_city ?? '-' }}
                                </small>
                            </td>

                            <td>
                                @if ($item->kod?->qr_status === 'ACTIVE')
                                    <span class="badge text-bg-success">Aktif</span>
                                @elseif ($item->kod?->qr_status === 'INACTIVE')
                                    <span class="badge text-bg-secondary">Tidak Aktif</span>
                                @else
                                    <span class="badge text-bg-warning">Belum Dijana</span>
                                @endif
                            </td>

                            <td>
                                {{ $item->kod?->generated_at?->format('d/m/Y, h:i A') ?? '-' }}
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('kodqr.show', $item) }}"
                                    class="btn btn-sm btn-outline-success"
                                    title="Lihat kod QR"
                                >
                                    <i class="bi bi-qr-code"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <i class="bi bi-buildings text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">
                                    Tiada aset bangunan didaftarkan.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-jadual :paginator="$bangunan" />
    </div>
@endsection