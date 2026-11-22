@extends('layouts.utama')

@section('tajuk', 'Tetapan Status Aset')

@section('kandungan')
    @php
        $namaJenis = \App\Models\StatusAset::JENIS[$jenis];
        [$statusAktif, $statusTidakAktif] = $status->partition(fn ($s) => $s->aktif());
    @endphp

    <div class="hero-tetapan mb-4 d-none d-md-block">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
        <p class="teks-penerangan mb-0 d-none d-md-block">Urus konfigurasi dan akses sistem.</p>
    </div>

    @include('tetapan._bar-apl', ['tajuk' => 'Status Aset'])

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3 d-none d-md-block">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            {{-- Kepala --}}
            <div class="kad-data kad-tetapan mb-3 d-none d-md-block">
                <div class="kepala-kad-tetapan">
                    <div class="d-flex align-items-center justify-content-between gap-3 w-100">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-tags"></i>
                            </div>

                            <div class="min-w-0">
                                <h2 class="h6 fw-bold mb-1">Status Aset</h2>
                                <p class="mb-0 text-secondary small">
                                    Senarai status premis (bangunan) dan status tanah yang dipaparkan dalam borang, penapis dan laporan.
                                </p>
                            </div>
                        </div>

                        <button type="button" class="btn btn-utama butang-tambah-ikon flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalStatusBaru" title="Tambah Status" aria-label="Tambah Status">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tab jenis --}}
            <nav class="tab-status-aset mb-3" aria-label="Jenis status">
                @foreach (\App\Models\StatusAset::JENIS as $kodJenis => $labelJenis)
                    <a href="{{ route('tetapan.status-aset', ['jenis' => $kodJenis]) }}" class="{{ $jenis === $kodJenis ? 'aktif' : '' }}" @if ($jenis === $kodJenis) aria-current="page" @endif>
                        <i class="bi {{ $kodJenis === 'BANGUNAN' ? 'bi-building' : 'bi-map' }} me-1"></i>{{ $labelJenis }}
                    </a>
                @endforeach
            </nav>

            {{-- ===================== TELEFON: senarai status ===================== --}}
            <div class="d-md-none tetapan-apl">
                @if ($status->isEmpty())
                    <div class="kad-kosong">
                        <div class="ilustrasi-kosong"><i class="bi bi-tags"></i></div>
                        <h3>Tiada status</h3>
                        <p>Tekan butang + untuk tambah status pertama.</p>
                    </div>
                @endif

                @if ($statusAktif->isNotEmpty())
                    <p class="label-kumpulan-apl">Aktif · {{ $statusAktif->count() }}</p>

                    <div class="kad-senarai-apl mb-3">
                        @foreach ($statusAktif as $item)
                            @include('tetapan._baris-status-aset', ['item' => $item])
                        @endforeach
                    </div>
                @endif

                @if ($statusTidakAktif->isNotEmpty())
                    <button type="button" class="label-kumpulan-apl butang-lipat-apl collapsed" data-bs-toggle="collapse" data-bs-target="#statusTidakAktif" aria-expanded="false" aria-controls="statusTidakAktif">
                        Tidak aktif · {{ $statusTidakAktif->count() }}
                        <i class="bi bi-chevron-down" aria-hidden="true"></i>
                    </button>

                    <div class="collapse" id="statusTidakAktif">
                        <div class="kad-senarai-apl mb-3 senarai-tidak-aktif-apl">
                            @foreach ($statusTidakAktif as $item)
                                @include('tetapan._baris-status-aset', ['item' => $item])
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="nota-kumpulan-apl">
                    Tekan status untuk ubah nama, warna atau status. Status tidak boleh dihapus;
                    matikan suis Aktif untuk sembunyikannya daripada borang. Rekod lama kekal memaparkan status asal.
                </p>
            </div>

            {{-- ===================== DESKTOP: jadual ===================== --}}
            <div class="kad-data mb-4 d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 jadual-sistem" data-tanpa-kad>
                        <thead>
                            <tr>
                                <th>Bil.</th>
                                <th>{{ $namaJenis }}</th>
                                <th>Kod</th>
                                @if ($jenis === 'BANGUNAN')
                                    <th>Boleh Diiklankan</th>
                                @endif
                                <th>Digunakan</th>
                                <th>Status</th>
                                <th class="text-end">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($status as $item)
                                <tr @class(['opacity-75' => ! $item->aktif()])>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="lencana-jadual {{ $item->kelasWarna() }}" style="{{ $item->gayaWarna() }}">{{ $item->sts_label }}</span></td>
                                    <td><code class="small text-secondary">{{ $item->sts_kod }}</code></td>
                                    @if ($jenis === 'BANGUNAN')
                                        <td>
                                            @if ($item->sts_boleh_iklan)
                                                <span class="lencana-jadual utama"><i class="bi bi-megaphone-fill me-1"></i>Ya</span>
                                            @else
                                                <span class="text-secondary small">Tidak</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-secondary small">{{ (int) ($penggunaan[$item->sts_kod] ?? 0) }} rekod</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-switch suis-status-tetapan mb-0" title="Aktif / Tidak aktif">
                                                <input class="form-check-input" type="checkbox" role="switch" data-togol-status="{{ route('tetapan.status-aset.status', $item) }}" data-modal-sasaran="#modalStatus{{ $item->sts_id }}" aria-label="Status {{ $item->sts_label }} aktif" @checked($item->aktif())>
                                            </div>

                                            <span class="lencana-jadual {{ $item->aktif() ? 'utama' : 'kelabu' }}" data-lencana-status>
                                                {{ \App\Support\Paparan::label($item->sts_status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalStatus{{ $item->sts_id }}" aria-label="Ubah status {{ $item->sts_label }}">
                                            <i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline ms-1">Ubah</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-center">
                                        <i class="bi bi-tags text-secondary fs-2 d-block mb-2"></i>
                                        <span class="text-secondary">Tiada status. Klik butang + untuk menambah status.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="makluman-tetapan d-none d-md-block">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Status tidak boleh dihapus supaya rekod aset lama kekal lengkap. Matikan suis <strong>Aktif</strong> untuk menyembunyikannya daripada borang.
                Kod dijana sekali dan tidak boleh diubah; nama (label) boleh diubah bila-bila masa.
            </div>
        </div>
    </div>

    {{-- Butang tambah terapung (telefon) --}}
    <button type="button" class="fab-tambah d-md-none" data-bs-toggle="modal" data-bs-target="#modalStatusBaru" aria-label="Tambah Status" title="Tambah Status">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
    </button>

    <div class="ruang-fab d-md-none" aria-hidden="true"></div>

    {{-- Modal tambah --}}
    @include('tetapan._modal-status-aset', [
        'idModal' => 'modalStatusBaru',
        'tajuk' => 'Tambah ' . $namaJenis,
        'action' => route('tetapan.status-aset.simpan'),
        'method' => 'POST',
        'statusAset' => null,
    ])

    {{-- Modal ubah --}}
    @foreach ($status as $item)
        @include('tetapan._modal-status-aset', [
            'idModal' => 'modalStatus' . $item->sts_id,
            'tajuk' => 'Ubah ' . $namaJenis,
            'action' => route('tetapan.status-aset.kemaskini', $item),
            'method' => 'PUT',
            'statusAset' => $item,
        ])
    @endforeach
@endsection

@push('skrip')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Buka semula modal yang gagal disahkan
            @if ($errors->any() && old('_modal'))
                const modalRalat = document.getElementById(@json(old('_modal')));

                if (modalRalat) {
                    bootstrap.Modal.getOrCreateInstance(modalRalat).show();
                }
            @endif

            // Pratonton badge dalam modal
            document.querySelectorAll('[data-borang-status-aset]').forEach((borang) => {
                const pratonton = borang.querySelector('[data-pratonton-status]');
                const label = borang.querySelector('[name="sts_label"]');

                const radioSuai = borang.querySelector('[data-radio-warna-suai]');
                const inputSuai = borang.querySelector('[data-input-warna-suai]');
                const labelSuai = borang.querySelector('[data-label-warna-suai]');

                // Versi gelap warna (teks badge) — sama seperti StatusAset::gayaWarna()
                const gelapkan = (hex) => '#' + [1, 3, 5]
                    .map((i) => Math.round(parseInt(hex.slice(i, i + 2), 16) * 0.55).toString(16).padStart(2, '0'))
                    .join('');

                const kemaskini = () => {
                    const warna = borang.querySelector('[name="sts_warna"]:checked')?.value || 'kelabu';
                    pratonton.textContent = label.value.trim() || 'Nama status';

                    if (warna === 'suai') {
                        pratonton.className = 'lencana-jadual status-suai';
                        pratonton.style.setProperty('--warna-status', inputSuai.value);
                        pratonton.style.setProperty('--warna-status-teks', gelapkan(inputSuai.value));
                    } else {
                        pratonton.className = 'lencana-jadual status-' + warna;
                        pratonton.removeAttribute('style');
                    }
                };

                // Pilih warna lain: tanda radio "suai" dan warnakan bulatan
                inputSuai?.addEventListener('input', () => {
                    radioSuai.checked = true;
                    labelSuai.classList.add('ada-warna');
                    labelSuai.style.setProperty('--warna-status', inputSuai.value);
                    kemaskini();
                });

                label.addEventListener('input', kemaskini);
                borang.querySelectorAll('[name="sts_warna"]').forEach((r) => r.addEventListener('change', kemaskini));
                kemaskini();
            });
        });
    </script>
@endpush
