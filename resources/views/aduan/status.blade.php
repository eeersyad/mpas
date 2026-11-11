@extends('layouts.awam')

@section('tajuk', 'Status Aduan ' . $aduan->cmp_ref_no)

@section('kandungan')
    @php
        $ditolak = $aduan->ditolak();

        $langkah = [
            'BARU' => ['label' => 'Aduan diterima', 'ikon' => 'bi-inbox'],
            'DITERIMA' => ['label' => 'Disemak dan diterima BPH', 'ikon' => 'bi-clipboard-check'],
            'DALAM_PENYELENGGARAAN' => ['label' => 'Dalam penyelenggaraan', 'ikon' => 'bi-tools'],
            'SELESAI' => ['label' => 'Selesai', 'ikon' => 'bi-check-circle'],
        ];

        $kedudukanSemasa = array_search($aduan->cmp_status, array_keys($langkah), true);
        $kedudukanSemasa = $kedudukanSemasa === false ? 0 : $kedudukanSemasa;
    @endphp

    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo MPAS" style="height: 56px;">
            </div>

            <section class="kad-awam p-4 p-md-5">
                <p class="small text-secondary fw-semibold mb-1">STATUS ADUAN</p>
                <h1 class="h4 fw-bold mb-1">{{ $aduan->cmp_ref_no }}</h1>
                <p class="text-secondary mb-4">{{ $aduan->bangunan?->asb_name ?? '-' }}</p>

                <dl class="row small mb-4">
                    <dt class="col-5 col-sm-4 text-secondary fw-semibold">Jenis kerosakan</dt>
                    <dd class="col-7 col-sm-8">{{ $aduan->cmp_damage_type ?? '-' }}</dd>

                    <dt class="col-5 col-sm-4 text-secondary fw-semibold">Tarikh aduan</dt>
                    <dd class="col-7 col-sm-8">{{ $aduan->cmp_received_date?->format('d/m/Y h:i A') ?? '-' }}</dd>

                    @if ($aduan->cmp_completed_at)
                        <dt class="col-5 col-sm-4 text-secondary fw-semibold">Tarikh selesai</dt>
                        <dd class="col-7 col-sm-8">{{ $aduan->cmp_completed_at->format('d/m/Y h:i A') }}</dd>
                    @endif
                </dl>

                @if ($ditolak)
                    <div class="alert alert-danger d-flex gap-3 mb-0">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                        <div>
                            <strong class="d-block mb-1">Aduan tidak dapat diteruskan</strong>
                            <span class="small">Sebab: {{ $aduan->sebabDitolak() ?: '-' }}</span>
                        </div>
                    </div>
                @else
                    <ol class="list-unstyled mb-0">
                        @foreach ($langkah as $kod => $item)
                            @php
                                $selesai = $loop->index <= $kedudukanSemasa;
                                $semasa = $loop->index === $kedudukanSemasa;
                            @endphp

                            <li class="d-flex align-items-start gap-3 {{ $loop->last ? '' : 'pb-3' }}">
                                <span class="d-grid flex-shrink-0 rounded-circle {{ $selesai ? 'text-white' : 'text-secondary border' }}" style="width: 36px; height: 36px; place-items: center; background: {{ $selesai ? '#168A7E' : 'transparent' }};">
                                    <i class="bi {{ $item['ikon'] }}"></i>
                                </span>

                                <div class="pt-1">
                                    <strong class="{{ $selesai ? '' : 'text-secondary' }}">{{ $item['label'] }}</strong>

                                    @if ($semasa)
                                        <span class="badge rounded-pill ms-2" style="background: #E1F3F1; color: #0D625A;">Status semasa</span>
                                    @endif

                                    @if ($kod === 'SELESAI' && $aduan->cmp_status === 'SELESAI' && filled($aduan->cmp_completion_note))
                                        <p class="small text-secondary mb-0 mt-1">{{ $aduan->cmp_completion_note }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            <p class="text-center small text-secondary mt-4 mb-0">
                <a href="{{ route('aduan.status') }}">Semak aduan lain</a>
            </p>
        </div>
    </div>
@endsection
