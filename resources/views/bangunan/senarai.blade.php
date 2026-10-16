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

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>No. Rujukan</th>
                        <th>Nama Bangunan</th>
                        <th>Kategori</th>
                        <th>Status Premis</th>
                        <th>Penyewa Aktif</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($bangunan as $item)
                        <tr>
                            <td>{{ $bangunan->firstItem() + $loop->index }}</td>

                            <td class="fw-semibold">{{ $item->asb_ref_no }}</td>

                            <td>
                                <div class="fw-semibold">{{ $item->asb_name }}</div>
                                <small class="text-secondary">{{ $item->asb_city ?? '-' }}</small>
                            </td>

                            <td>{{ str_replace('_', ' ', $item->asb_category) }}</td>

                            <td>{{ str_replace('_', ' ', $item->asb_premise_status) }}</td>

                            <td>
                                {{ $item->penyewa->where('tnt_status', 'AKTIF')->count() }}
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route('bangunan.edit', $item) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Kemaskini aset bangunan"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <i class="bi bi-buildings text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">Tiada aset bangunan didaftarkan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bangunan->hasPages())
            <div class="p-3 border-top">
                {{ $bangunan->links() }}
            </div>
        @endif
    </div>
@endsection