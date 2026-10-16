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

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>No. Rujukan</th>
                        <th>Aset Bangunan</th>
                        <th>Status QR</th>
                        <th>Tarikh Dijana</th>
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

        @if ($bangunan->hasPages())
            <div class="p-3 border-top">
                {{ $bangunan->links() }}
            </div>
        @endif
    </div>
@endsection