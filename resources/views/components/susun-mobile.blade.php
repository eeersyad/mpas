{{--
    Dropdown "Susun ikut" untuk paparan telefon (pengganti klik tajuk kolum).
    Guna: <x-susun-mobile :pilihan="['nama' => 'Nama Bangunan', ...]" />
    Menggunakan parameter ?sort= & ?direction= yang sama seperti <x-susun>.
--}}
@props([
    'pilihan' => [],
])

@php
    $sortSemasa = request('sort');
    $arahSemasa = request('direction', 'desc');
    $asas = request()->except(['page', 'sort', 'direction']);

    $binaUrl = fn (array $parameter) => url()->current() . ($parameter ? '?' . http_build_query($parameter) : '');

    $senaraiPilihan = [
        ['url' => $binaUrl($asas), 'label' => 'Terkini', 'aktif' => blank($sortSemasa)],
    ];

    foreach ($pilihan as $medan => $label) {
        foreach (['asc' => 'menaik', 'desc' => 'menurun'] as $arah => $teksArah) {
            $senaraiPilihan[] = [
                'url' => $binaUrl([...$asas, 'sort' => $medan, 'direction' => $arah]),
                'label' => "{$label} ({$teksArah})",
                'aktif' => $sortSemasa === $medan && $arahSemasa === $arah,
            ];
        }
    }
@endphp

<div class="susun-mobile d-md-none mb-3">
    <label for="susunMobile" class="susun-mobile-label">
        <i class="bi bi-arrow-down-up"></i>
        Susun ikut
    </label>

    <select id="susunMobile" class="form-select form-select-sm" data-susun-mobile aria-label="Susun senarai">
        @foreach ($senaraiPilihan as $item)
            <option value="{{ $item['url'] }}" @selected($item['aktif'])>{{ $item['label'] }}</option>
        @endforeach
    </select>
</div>
