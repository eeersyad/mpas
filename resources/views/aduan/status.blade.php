@extends('layouts.awam')

@section('tajuk', 'Status Aduan ' . $aduan->cmp_ref_no)

@section('kandungan')
    @php
        $ditolak = $aduan->ditolak();

        $langkah = [
            'BARU' => ['label' => 'Aduan diterima', 'penerangan' => 'Aduan anda telah direkodkan dalam sistem.', 'ikon' => 'bi-inbox'],
            'DITERIMA' => ['label' => 'Disemak dan diterima', 'penerangan' => 'Aduan telah disemak oleh Bahagian Pengurusan Harta.', 'ikon' => 'bi-clipboard-check'],
            'DALAM_PENYELENGGARAAN' => ['label' => 'Dalam penyelenggaraan', 'penerangan' => 'Kerja pembaikan sedang dijalankan.', 'ikon' => 'bi-tools'],
            'SELESAI' => ['label' => 'Selesai', 'penerangan' => 'Kerosakan telah dibaiki.', 'ikon' => 'bi-check-circle'],
        ];

        $ringkasStatus = [
            'BARU' => ['label' => 'Aduan Diterima', 'peratus' => 25, 'warna' => 'primary'],
            'DITERIMA' => ['label' => 'Dalam Tindakan BPH', 'peratus' => 50, 'warna' => 'info'],
            'DALAM_PENYELENGGARAAN' => ['label' => 'Dalam Penyelenggaraan', 'peratus' => 75, 'warna' => 'warning'],
            'SELESAI' => ['label' => 'Selesai', 'peratus' => 100, 'warna' => 'success'],
        ][$aduan->cmp_status] ?? ['label' => \App\Support\Paparan::label($aduan->cmp_status), 'peratus' => 25, 'warna' => 'secondary'];

        $kedudukanSemasa = array_search($aduan->cmp_status, array_keys($langkah), true);
        $kedudukanSemasa = $kedudukanSemasa === false ? 0 : $kedudukanSemasa;
    @endphp

    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">

            <header class="awam-hero mb-4">
                <div class="awam-logo mb-3">
                    <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo MPAS">
                </div>

                <div class="maklumat-aset-awam">
                    <h1>{{ $aduan->bangunan?->asb_name ?? 'Status Aduan' }}</h1>

                    <span class="jumlah-penyewa-awam">
                        <i class="bi bi-hash"></i>{{ $aduan->cmp_ref_no }}
                    </span>
                </div>
            </header>

            {{-- Ringkasan status --}}
            <section class="kad-awam mb-4">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="kad-aduan-ikon {{ $ditolak ? 'ditolak' : strtolower($aduan->cmp_status) }}">
                        <i class="bi {{ $ditolak ? 'bi-x-circle' : 'bi-tools' }}"></i>
                    </div>

                    @if ($ditolak)
                        <span class="badge text-bg-danger">Tidak Diteruskan</span>
                    @else
                        <span class="badge text-bg-{{ $ringkasStatus['warna'] }}">{{ $ringkasStatus['label'] }}</span>
                    @endif
                </div>

                <div class="mt-3">
                    <strong class="d-block tajuk-kad-aduan">{{ $aduan->cmp_damage_type ? \App\Support\Paparan::label($aduan->cmp_damage_type) : '-' }}</strong>
                    <p class="text-secondary small mb-3">Aduan direkodkan pada {{ $aduan->cmp_received_date?->format('d/m/Y, h:i A') ?? '-' }}</p>

                    @unless ($ditolak)
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" role="progressbar" aria-valuenow="{{ $ringkasStatus['peratus'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-{{ $ringkasStatus['warna'] }}" style="width: {{ $ringkasStatus['peratus'] }}%"></div>
                            </div>
                            <small class="fw-bold text-secondary">{{ $ringkasStatus['peratus'] }}%</small>
                        </div>
                    @endunless
                </div>

                <dl class="senarai-maklumat-status">
                    <div>
                        <dt>No. rujukan</dt>
                        <dd>{{ $aduan->cmp_ref_no }}</dd>
                    </div>
                    <div>
                        <dt>Tarikh aduan</dt>
                        <dd>{{ $aduan->cmp_received_date?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    @if ($aduan->cmp_completed_at)
                        <div>
                            <dt>Tarikh selesai</dt>
                            <dd>{{ $aduan->cmp_completed_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            {{-- Perjalanan aduan --}}
            <section class="kad-awam mb-4">
                <div class="kepala-borang-awam mb-4">
                    <span class="ikon-borang-awam">
                        <i class="bi bi-signpost-split"></i>
                    </span>

                    <div>
                        <p class="teks-kecil mb-1">PERJALANAN ADUAN</p>
                        <h2 class="h6 fw-bold mb-0">Kemajuan Tindakan</h2>
                    </div>
                </div>

                @if ($ditolak)
                    <div class="kotak-ditolak-awam">
                        <i class="bi bi-x-circle-fill"></i>
                        <div>
                            <strong>Aduan tidak dapat diteruskan</strong>
                            <p class="mb-0">Sebab: {{ $aduan->sebabDitolak() ?: '-' }}</p>
                        </div>
                    </div>
                @else
                    <ol class="garis-masa-aduan">
                        @foreach ($langkah as $kod => $item)
                            @php
                                $selesai = $loop->index < $kedudukanSemasa || ($loop->index === $kedudukanSemasa && $kod === 'SELESAI');
                                $semasa = $loop->index === $kedudukanSemasa;
                            @endphp

                            <li @class(['selesai' => $selesai, 'semasa' => $semasa && ! $selesai, 'akan-datang' => $loop->index > $kedudukanSemasa])>
                                <span class="titik-garis-masa">
                                    <i class="bi {{ $selesai ? 'bi-check-lg' : $item['ikon'] }}"></i>
                                </span>

                                <div class="isi-garis-masa">
                                    <strong>
                                        {{ $item['label'] }}
                                        @if ($semasa)
                                            <span class="lencana-semasa-awam">Status semasa</span>
                                        @endif
                                    </strong>
                                    <p>{{ $item['penerangan'] }}</p>

                                    @if ($kod === 'SELESAI' && $aduan->cmp_status === 'SELESAI' && filled($aduan->cmp_completion_note))
                                        <div class="nota-selesai-awam">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $aduan->cmp_completion_note }}
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('aduan.status') }}" class="btn btn-light flex-fill butang-sekunder-awam">
                    <i class="bi bi-search me-2"></i>Semak aduan lain
                </a>
                <button type="button" class="btn btn-light flex-fill butang-sekunder-awam" data-salin-url-status>
                    <i class="bi bi-link-45deg me-2"></i>Salin pautan halaman ini
                </button>
            </div>

            <script>
                document.querySelector('[data-salin-url-status]')?.addEventListener('click', async (event) => {
                    const butang = event.currentTarget;
                    const asal = butang.innerHTML;

                    try {
                        await navigator.clipboard.writeText(window.location.href);
                    } catch {
                        const sementara = document.createElement('textarea');
                        sementara.value = window.location.href;
                        document.body.appendChild(sementara);
                        sementara.select();
                        document.execCommand('copy');
                        sementara.remove();
                    }

                    butang.innerHTML = '<i class="bi bi-check2 me-2"></i>Pautan disalin';
                    setTimeout(() => { butang.innerHTML = asal; }, 1800);
                });
            </script>
        </div>
    </div>
@endsection
