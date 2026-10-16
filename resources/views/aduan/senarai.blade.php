@extends('layouts.utama')

@section('tajuk', 'Aduan')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ADUAN</p>
            <h1 class="tajuk-halaman mb-2">Aduan Aset Bangunan</h1>
            <p class="teks-penerangan mb-0">
                Senarai aduan kerosakan aset bangunan.
            </p>
        </div>

        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>

                @foreach (['BARU', 'DITERIMA', 'DALAM_PENYELENGGARAAN', 'SELESAI'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>
                        {{ str_replace('_', ' ', $status) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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

                            <td>{{ $item->cmp_damage_type ?? '-' }}</td>

                            <td>
                                <span class="badge {{ $warnaStatus }}">
                                    {{ str_replace('_', ' ', $item->cmp_status) }}
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

        @if ($aduan->hasPages())
            <div class="p-3 border-top">
                {{ $aduan->links() }}
            </div>
        @endif
    </div>
@endsection