@props(['paginator'])

@php
    $hadSemasa = (int) request('had', 15);
@endphp

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3 border-top">
    <div class="d-flex align-items-center gap-2">
        <small class="text-secondary">Papar</small>

        <form method="GET">
            @foreach (request()->query() as $nama => $nilai)
                @if (!in_array($nama, ['had', 'page'], true) && !is_array($nilai))
                    <input type="hidden" name="{{ $nama }}" value="{{ $nilai }}">
                @endif
            @endforeach

            <select
                name="had"
                class="form-select form-select-sm"
                style="width: auto;"
                onchange="this.form.submit()"
                aria-label="Bilangan rekod setiap halaman"
            >
                @foreach ([15, 30, 50, 100] as $had)
                    <option value="{{ $had }}" @selected($hadSemasa === $had)>
                        {{ $had }}
                    </option>
                @endforeach
            </select>
        </form>

        <small class="text-secondary">rekod</small>
    </div>

    <small class="text-secondary">
        @if ($paginator->total() > 0)
            Memaparkan {{ $paginator->firstItem() }} hingga {{ $paginator->lastItem() }}
            daripada {{ $paginator->total() }} rekod
        @else
            Tiada rekod ditemui
        @endif
    </small>
</div>

@if ($paginator->hasPages())
    <div class="px-3 pb-3">
        {{ $paginator->onEachSide(1)->links() }}
    </div>
@endif