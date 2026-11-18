@extends('layouts.utama')

@section('tajuk', 'Aset Bangunan')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1 d-none d-md-block">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Aset Bangunan <span class="lencana-bilangan-tajuk d-md-none">{{ number_format($bangunan->total()) }}</span></h1>
            <p class="teks-penerangan mb-0 d-none d-md-block">
                Senarai aset bangunan yang telah didaftarkan.
            </p>
        </div>

        <a href="{{ route('bangunan.create') }}" class="btn btn-primary px-3 py-2 d-none d-md-inline-block">
            <i class="bi bi-plus-circle-fill me-2"></i>Daftar Aset Bangunan
        </a>
    </div>

    <form
        method="GET"
        class="panel-penapis mb-4 d-none d-md-block"
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
                    placeholder="No. rujukan, nama, bandar, kategori atau penyewa..."
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

    @php
        // Penapis untuk paparan telefon (bottom sheet)
        $penapisMobile = [
            'status_premis' => ['label' => 'Status Premis', 'pilihan' => ['SEWA', 'TIDAK_DISEWA', 'PAJAK', 'TIDAK_DIPAJAK', 'KEGUNAAN_MDM', 'DILUPUS']],
            'status_penyelenggaraan' => ['label' => 'Status Penyelenggaraan', 'pilihan' => ['RENDAH', 'SEDERHANA', 'KRITIKAL']],
            'perjanjian' => ['label' => 'Perjanjian Penyewa', 'pilihan' => ['TAMAT' => 'Telah tamat', 'HAMPIR' => 'Tamat dalam 3 bulan']],
        ];
    @endphp

    <x-penapis-mobile placeholder="Cari rujukan, nama, bandar atau penyewa..." :penapis="$penapisMobile" :susun="['rujukan' => 'No. Rujukan', 'nama' => 'Nama', 'status_premis' => 'Status Premis', 'penyelenggaraan' => 'Penyelenggaraan', 'penyewa' => 'Penyewa']" :url-semula="route('bangunan.index')" />

    <div class="petunjuk-perjanjian mb-2 d-none d-md-flex">
        <span><i class="titik-petunjuk tamat"></i>Perjanjian penyewa telah tamat</span>
        <span><i class="titik-petunjuk hampir"></i>Perjanjian tamat dalam 3 bulan</span>
    </div>

    <div class="kad-data">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 jadual-sistem jadual-bangunan" data-kad-apl="sembunyi|sub|tajuk|meta=No. fail:|lencana|lencana|info|tindakan">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th><x-susun medan="rujukan" label="No. Rujukan" /></th>
                        <th><x-susun medan="nama" label="Nama Bangunan" /></th>
                        <th><x-susun medan="no_fail" label="No. Fail" /></th>
                        <th><x-susun medan="status_premis" label="Status Premis" /></th>
                        <th><x-susun medan="penyelenggaraan" label="Status Penyelenggaraan" /></th>
                        <th><x-susun medan="penyewa" label="Penyewa Aktif" /></th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($bangunan as $item)
                        @php
                            $statusPerjanjian = match (true) {
                                $item->perjanjian_tamat_count > 0 => ['kelas' => 'baris-perjanjian-tamat', 'tajuk' => 'Terdapat perjanjian penyewa yang telah tamat'],
                                $item->perjanjian_hampir_tamat_count > 0 => ['kelas' => 'baris-perjanjian-hampir', 'tajuk' => 'Terdapat perjanjian penyewa yang akan tamat dalam 3 bulan'],
                                default => ['kelas' => '', 'tajuk' => ''],
                            };
                        @endphp

                        <tr class="baris-boleh-klik {{ $statusPerjanjian['kelas'] }}" data-baris-butiran data-url="{{ route('bangunan.butiran', $item) }}" tabindex="0" role="button" aria-label="Lihat butiran {{ $item->asb_name }}" @if ($statusPerjanjian['tajuk']) title="{{ $statusPerjanjian['tajuk'] }}" @endif>
                            <td>{{ $bangunan->firstItem() + $loop->index }}</td>

                            <td class="fw-semibold">
                                {{ $item->asb_ref_no }}
                                @if ($item->asb_mukim)
                                    <div class="baris-mukim"><span class="lencana-jadual mukim"><i class="bi bi-pin-map" aria-hidden="true"></i> {{ \App\Support\Paparan::label($item->asb_mukim) }}</span></div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $item->asb_name }}</div>

                                @if ($statusPerjanjian['kelas'])
                                    <div class="d-md-none">
                                        <span class="tag-perjanjian {{ $item->perjanjian_tamat_count > 0 ? 'tamat' : 'hampir' }}">
                                            <i class="bi {{ $item->perjanjian_tamat_count > 0 ? 'bi-exclamation-triangle-fill' : 'bi-hourglass-split' }}" aria-hidden="true"></i>
                                            {{ $item->perjanjian_tamat_count > 0 ? 'Perjanjian tamat' : 'Tamat dalam 3 bulan' }}
                                        </span>
                                    </div>
                                @endif
                                <div class="mt-2">
                                    <span class="lencana-jadual kelabu">
                                        {{ \App\Support\Paparan::label($item->asb_category) }}
                                    </span>
                                </div>
                            </td>

                            <td class="teks-no-fail" title="{{ $item->asb_no_file }}">
                                {{ $item->asb_no_file ?: '-' }}

                                {{-- Telefon: nama penyewa aktif di bawah no. fail --}}
                                @if ($item->penyewaAktif->isNotEmpty())
                                    <span class="penyewa-kad-apl d-md-none">
                                        <i class="bi bi-people" aria-hidden="true"></i>
                                        <span class="nama">{{ $item->penyewaAktif->first()->tnt_name }}</span>
                                        @if ($item->penyewaAktif->count() > 1)
                                            <span class="lagi">+{{ $item->penyewaAktif->count() - 1 }}</span>
                                        @endif
                                    </span>
                                @endif
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

                                <div class="tarikh-pemeriksaan-jadual" title="Tarikh pemeriksaan penyelenggaraan terkini">
                                    @if ($item->tarikh_pemeriksaan_terkini)
                                        Diperiksa: {{ \Illuminate\Support\Carbon::parse($item->tarikh_pemeriksaan_terkini)->format('d/m/Y') }}
                                    @else
                                        <span class="belum">Belum diperiksa</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="lencana-bilangan">
                                    <i class="bi bi-people"></i>
                                    {{ $item->penyewa_aktif_count }} penyewa
                                </span>

                                @if ($item->penyewaAktif->isNotEmpty())
                                    @php
                                        $namaPenyewa = $item->penyewaAktif->pluck('tnt_name');
                                    @endphp

                                    <div class="nama-penyewa-senarai d-none d-md-block" title="{{ $namaPenyewa->implode(', ') }}">
                                        @foreach ($namaPenyewa->take(2) as $nama)
                                            <span class="nama">{{ $nama }}</span>
                                        @endforeach

                                        @if ($namaPenyewa->count() > 2)
                                            <span class="lagi">+{{ $namaPenyewa->count() - 2 }} lagi</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td class="text-end">
                                {{-- Menu titik tiga (telefon) --}}
                                <div class="dropdown d-md-none">
                                    <button type="button" class="btn butang-menu-kad" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilihan tindakan">
                                        <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end menu-kad">
                                        <li><button type="button" class="dropdown-item" data-buka-butiran-kad><i class="bi bi-eye" aria-hidden="true"></i>Lihat butiran</button></li>
                                        @include('pdf._item-muat-turun-apl', ['jenis' => 'bangunan', 'aset' => $item])
                                        <li><a class="dropdown-item" href="{{ route('bangunan.edit', $item) }}"><i class="bi bi-pencil-square" aria-hidden="true"></i>Kemaskini</a></li>
                                    </ul>
                                </div>

                                <div class="d-none d-md-inline-flex gap-2">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Muat turun" aria-label="Pilihan muat turun aset bangunan">
                                            <i class="bi bi-download"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end menu-kad">
                                            @include('pdf._pilihan-muat-turun', ['jenis' => 'bangunan', 'aset' => $item])
                                        </ul>
                                    </div>
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
                            <td colspan="9" class="py-5 text-center">
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
    {{-- Butang tambah terapung (telefon) --}}
    <a href="{{ route('bangunan.create') }}" class="fab-tambah d-md-none" aria-label="Daftar Aset Bangunan" title="Daftar Aset Bangunan">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
    </a>

    <div class="ruang-fab d-md-none" aria-hidden="true"></div>
@endsection