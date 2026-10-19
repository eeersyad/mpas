@extends('layouts.utama')

@section('tajuk', $konfigurasi['tajuk'])

@section('kandungan')
    @php
        $parameterEksport = array_merge(
            ['jenis' => $jenis],
            request()->except(['page', 'had'])
        );
    @endphp
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Laporan
            </a>

            <p class="teks-kecil mb-1">LAPORAN SISTEM</p>
            <h1 class="tajuk-halaman mb-2">
                <i class="bi {{ $konfigurasi['ikon'] }} me-2 text-primary"></i>
                {{ $konfigurasi['tajuk'] }}
            </h1>

            <p class="teks-penerangan mb-0">{{ $konfigurasi['penerangan'] }}</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a
                href="{{ route('laporan.pdf', $parameterEksport) }}"
                class="btn btn-outline-danger btn-sm"
            >
                <i class="bi bi-file-earmark-pdf me-1"></i>Eksport PDF
            </a>

            <a
                href="{{ route('laporan.excel', $parameterEksport) }}"
                class="btn btn-outline-success btn-sm"
            >
                <i class="bi bi-file-earmark-excel me-1"></i>Eksport Excel
            </a>

            <div class="lencana-laporan-utama">
                <i class="bi bi-table"></i>
                {{ number_format($rekod->total()) }} rekod
            </div>
        </div>
    </div>

    <form method="GET" class="panel-penapis mb-4">
        <input type="hidden" name="had" value="{{ request('had', 15) }}">

        <div class="row g-3 align-items-end">
            @if (in_array($jenis, ['bangunan', 'tanah'], true))
                <div class="col-12 col-xl-5">
                    <label for="cari" class="label-penapis">
                        <i class="bi bi-search"></i> Carian Rekod
                    </label>

                    <input
                        type="search"
                        id="cari"
                        name="cari"
                        value="{{ request('cari') }}"
                        class="form-control kawalan-penapis"
                        placeholder="{{ $jenis === 'bangunan' ? 'Rujukan, nama bangunan atau bandar...' : 'Rujukan, lot, lokasi atau hakmilik...' }}"
                    >
                </div>
            @else
                <div class="col-12 col-xl-4">
                    <label for="cari" class="label-penapis">
                        <i class="bi bi-search"></i> Carian Aduan
                    </label>

                    <input
                        type="search"
                        id="cari"
                        name="cari"
                        value="{{ request('cari') }}"
                        class="form-control kawalan-penapis"
                        placeholder="Aset, rujukan, kerosakan atau catatan..."
                    >
                </div>
            @endif

            @if ($jenis === 'bangunan')
                <div class="col-12 col-md-6 col-xl-2">
                    <label for="statusPremis" class="label-penapis">
                        <i class="bi bi-building-check"></i> Status Premis
                    </label>

                    <select id="statusPremis" name="status_premis" class="form-select kawalan-penapis">
                        <option value="">Semua Status</option>

                        @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEGUNAAN_MDM', 'DILUPUS'] as $status)
                            <option value="{{ $status }}" @selected(request('status_premis') === $status)>
                                {{ \App\Support\Paparan::label($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <label for="statusPenyelenggaraan" class="label-penapis">
                        <i class="bi bi-tools"></i> Penyelenggaraan
                    </label>

                    <select id="statusPenyelenggaraan" name="status_penyelenggaraan" class="form-select kawalan-penapis">
                        <option value="">Semua Status</option>

                        @foreach (['RENDAH', 'SEDERHANA', 'KRITIKAL'] as $status)
                            <option value="{{ $status }}" @selected(request('status_penyelenggaraan') === $status)>
                                {{ \App\Support\Paparan::label($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($jenis === 'tanah')
                <div class="col-12 col-md-6 col-xl-2">
                    <label for="statusHakmilik" class="label-penapis">
                        <i class="bi bi-file-earmark-text"></i> Status Hakmilik
                    </label>

                    <select id="statusHakmilik" name="status_hakmilik" class="form-select kawalan-penapis">
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

                    <select id="jenisHakmilik" name="jenis_hakmilik" class="form-select kawalan-penapis">
                        <option value="">Semua Jenis</option>

                        @foreach (['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEKAL'] as $jenisHakmilik)
                            <option value="{{ $jenisHakmilik }}" @selected(request('jenis_hakmilik') === $jenisHakmilik)>
                                {{ \App\Support\Paparan::label($jenisHakmilik) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (in_array($jenis, ['aduan', 'penyelenggaraan'], true))
                <div class="col-12 col-md-6 col-xl-2">
                    <label for="status" class="label-penapis">
                        <i class="bi bi-clipboard-check"></i> Status Aduan
                    </label>

                    <select id="status" name="status" class="form-select kawalan-penapis">
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

                    <select id="jenisPengadu" name="jenis_pengadu" class="form-select kawalan-penapis">
                        <option value="">Semua Pengadu</option>
                        <option value="PENYEWA" @selected(request('jenis_pengadu') === 'PENYEWA')>Penyewa</option>
                        <option value="AWAM" @selected(request('jenis_pengadu') === 'AWAM')>Orang Awam</option>
                    </select>
                </div>

                <div class="col-6 col-xl-2">
                    <label for="tarikhMula" class="label-penapis">
                        <i class="bi bi-calendar-event"></i> Tarikh Mula
                    </label>

                    <input
                        type="date"
                        id="tarikhMula"
                        name="tarikh_mula"
                        value="{{ request('tarikh_mula') }}"
                        class="form-control kawalan-penapis"
                    >
                </div>

                <div class="col-6 col-xl-2">
                    <label for="tarikhTamat" class="label-penapis">
                        <i class="bi bi-calendar-check"></i> Tarikh Tamat
                    </label>

                    <input
                        type="date"
                        id="tarikhTamat"
                        name="tarikh_tamat"
                        value="{{ request('tarikh_tamat') }}"
                        class="form-control kawalan-penapis"
                    >
                </div>
            @endif

            <div class="col-12 col-xl-3">
                <div class="d-flex gap-2 tindakan-penapis">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Jana Laporan
                    </button>

                    <a href="{{ route('laporan.papar', $jenis) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Semula
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 jadual-sistem">
                <thead>
                    @if ($jenis === 'bangunan')
                        <tr>
                            <th>Bil.</th>
                            <th>No. Rujukan</th>
                            <th>Nama Bangunan</th>
                            <th>Status Premis</th>
                            <th>Penyelenggaraan</th>
                            <th>Penyewa Aktif</th>
                        </tr>
                    @elseif ($jenis === 'tanah')
                        <tr>
                            <th>Bil.</th>
                            <th>No. Rujukan</th>
                            <th>No. Lot</th>
                            <th>Lokasi</th>
                            <th>Status Hakmilik</th>
                            <th>Jenis Hakmilik</th>
                            <th>Keluasan</th>
                        </tr>
                    @else
                        <tr>
                            <th>Bil.</th>
                            <th>Tarikh Aduan</th>
                            <th>Aset Bangunan</th>
                            <th>Jenis Kerosakan</th>
                            <th>Pengadu</th>
                            <th>Status</th>
                        </tr>
                    @endif
                </thead>

                <tbody>
                    @forelse ($rekod as $item)
                        @if ($jenis === 'bangunan')
                            <tr>
                                <td>{{ $rekod->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $item->asb_ref_no }}</td>
                                <td>
                                    <strong class="d-block">{{ $item->asb_name }}</strong>
                                    <small class="text-secondary">{{ $item->asb_city ?? '-' }}</small>
                                </td>
                                <td>{{ \App\Support\Paparan::label($item->asb_premise_status) }}</td>
                                <td>{{ \App\Support\Paparan::label($item->asb_maintenance_status) }}</td>
                                <td>{{ $item->penyewa_aktif_count }}</td>
                            </tr>
                        @elseif ($jenis === 'tanah')
                            <tr>
                                <td>{{ $rekod->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $item->asl_ref_no }}</td>
                                <td>{{ $item->asl_lot_no }}</td>
                                <td>{{ $item->asl_location }}</td>
                                <td>{{ \App\Support\Paparan::label($item->asl_status_hakmilik) }}</td>
                                <td>{{ \App\Support\Paparan::label($item->asl_hakmilik_type) }}</td>
                                <td>{{ number_format($item->asl_land_size, 2) }} m²</td>
                            </tr>
                        @else
                            <tr>
                                <td>{{ $rekod->firstItem() + $loop->index }}</td>
                                <td>
                                    {{ $item->cmp_received_date?->format('d/m/Y') }}
                                    <small class="d-block text-secondary">
                                        {{ $item->cmp_received_date?->format('h:i A') }}
                                    </small>
                                </td>
                                <td>
                                    <strong class="d-block">{{ $item->bangunan?->asb_name ?? '-' }}</strong>
                                    <small class="text-secondary">{{ $item->bangunan?->asb_ref_no ?? '-' }}</small>
                                </td>
                                <td>{{ \App\Support\Paparan::label($item->cmp_damage_type) }}</td>
                                <td>{{ $item->tnt_id ? 'Penyewa' : 'Orang Awam' }}</td>
                                <td>{{ \App\Support\Paparan::label($item->cmp_status) }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <i class="bi bi-file-earmark-x text-secondary fs-2 d-block mb-2"></i>
                                <span class="text-secondary">Tiada rekod ditemui bagi laporan ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-jadual :paginator="$rekod" />
    </div>
@endsection