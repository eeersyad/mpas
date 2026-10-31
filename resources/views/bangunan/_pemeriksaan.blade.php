@php
    $ringkasan = $rekod->ringkasanNilai();

    $warnaNilai = [
        'BAIK' => 'success',
        'ROSAK' => 'danger',
        'TIDAK_BERKAITAN' => 'secondary',
    ];
@endphp

{{-- Maklumat rekod + ringkasan --}}
<div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
    <div class="small text-secondary">
        <div>
            <i class="bi bi-calendar3 me-1"></i>
            Tarikh pemeriksaan:
            <strong class="text-body">{{ $rekod->asm_inspection_date->format('d/m/Y') }}</strong>
        </div>
        <div>
            <i class="bi bi-person-check me-1"></i>
            Pemeriksa:
            <strong class="text-body">{{ $rekod->pemeriksa?->usr_name ?? '-' }}</strong>
        </div>

        @if ($rekod->updated_at && $rekod->created_at && $rekod->updated_at->gt($rekod->created_at))
            <div>
                <i class="bi bi-pencil-square me-1"></i>
                Dikemas kini oleh {{ $rekod->dikemaskiniOleh?->usr_name ?? '-' }}
                pada {{ $rekod->updated_at->format('d/m/Y h:i A') }}
            </div>
        @endif
    </div>

    <div class="d-flex flex-wrap align-items-start gap-2">
        @foreach ($ringkasan as $nilai => $jumlah)
            <span class="badge rounded-pill bg-{{ $warnaNilai[$nilai] }}-subtle text-{{ $warnaNilai[$nilai] }}-emphasis">
                {{ \App\Support\Paparan::label($nilai) }}: {{ $jumlah }}
            </span>
        @endforeach
    </div>
</div>

{{-- Keputusan setiap komponen dan item --}}
@forelse ($rekod->asm_items ?? [] as $komponen)
    <div class="border rounded-3 mb-2">
        <div class="px-3 py-2 bg-light border-bottom rounded-top-3 small fw-semibold">
            {{ $loop->iteration }}. {{ $komponen['nama'] ?? '-' }}
        </div>

        @foreach ($komponen['item'] ?? [] as $item)
            @php
                $warna = $warnaNilai[$item['nilai'] ?? ''] ?? 'light';
            @endphp

            <div class="d-flex justify-content-between align-items-center gap-2 px-3 py-2 small {{ $loop->last ? '' : 'border-bottom' }}">
                <span>{{ $item['nama'] ?? '-' }}</span>

                <span class="badge bg-{{ $warna }}-subtle text-{{ $warna }}-emphasis text-nowrap">
                    {{ \App\Support\Paparan::label($item['nilai'] ?? null) }}
                </span>
            </div>
        @endforeach
    </div>
@empty
    <p class="text-secondary small mb-0">Tiada keputusan item direkodkan.</p>
@endforelse

{{-- Catatan --}}
@if (filled($rekod->asm_note))
    <dl class="butiran-medan mt-3 mb-0">
        <dt>Catatan Keseluruhan</dt>
        <dd>{!! nl2br(e($rekod->asm_note)) !!}</dd>
    </dl>
@endif