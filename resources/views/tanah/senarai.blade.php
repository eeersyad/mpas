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

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>No. Rujukan</th>
                        <th>No. Lot</th>
                        <th>Lokasi</th>
                        <th>Keluasan</th>
                        <th>Status Hakmilik</th>
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
                            <td>{{ $item->asl_status_hakmilik ?? '-' }}</td>
                            <td class="text-end">
                                <a
                                    href="{{ route('tanah.edit', $item) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Kemaskini aset tanah"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <i class="bi bi-map text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">Tiada aset tanah didaftarkan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tanah->hasPages())
            <div class="p-3 border-top">
                {{ $tanah->links() }}
            </div>
        @endif
    </div>
@endsection