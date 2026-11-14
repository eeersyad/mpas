{{-- Bar aplikasi (telefon sahaja). $borang (pilihan) = id borang untuk butang Simpan; $kembali (pilihan) = URL butang ← --}}
<div class="bar-apl-halaman d-md-none">
    <a href="{{ $kembali ?? route('tetapan.index') }}" class="butang-bar-apl" aria-label="Kembali">
        <i class="bi bi-arrow-left" aria-hidden="true"></i>
    </a>

    <span class="tajuk-bar-apl text-truncate">{{ $tajuk }}</span>

    @if (! empty($borang))
        <button type="submit" form="{{ $borang }}" class="butang-simpan-bar-apl" data-simpan-bar="{{ $borang }}" disabled>Simpan</button>
    @else
        <span class="butang-bar-apl invisible" aria-hidden="true"></span>
    @endif
</div>
