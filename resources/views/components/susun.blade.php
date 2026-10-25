@props([
    'medan',
    'label',
])

@php
    $sortSemasa = request('sort');
    $arahSemasa = request('direction', 'desc');
    $aktif = $sortSemasa === $medan;

    $arahSeterusnya = $aktif && $arahSemasa === 'asc'
        ? 'desc'
        : 'asc';

    $query = array_merge(
        request()->except(['page', 'sort', 'direction']),
        [
            'sort' => $medan,
            'direction' => $arahSeterusnya,
        ]
    );

    $ikon = $aktif
        ? ($arahSemasa === 'asc' ? 'bi-sort-up' : 'bi-sort-down')
        : 'bi-arrow-down-up';
@endphp

<a
    href="{{ url()->current() . '?' . http_build_query($query) }}"
    class="pautan-susun {{ $aktif ? 'aktif' : '' }}"
    aria-label="Susun mengikut {{ $label }} {{ $aktif && $arahSemasa === 'asc' ? 'menurun' : 'menaik' }}"
>
    <span>{{ $label }}</span>
    <i class="bi {{ $ikon }}" aria-hidden="true"></i>
</a>