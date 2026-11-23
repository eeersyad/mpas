@php
    $senaraiGambar = $rekod->gambar;
@endphp

<div class="gambar-pemeriksaan" data-gambar-pemeriksaan="{{ $rekod->asm_id }}">
    <div class="kepala-gambar-pemeriksaan">
        <span class="tajuk-gambar-pemeriksaan">
            <i class="bi bi-images me-1" aria-hidden="true"></i>
            Gambar Pemeriksaan
            <small class="text-secondary fw-normal">({{ $senaraiGambar->count() }})</small>
        </span>
    </div>

    @if ($senaraiGambar->isEmpty())
        <p class="text-secondary small mb-0">Tiada gambar dimuat naik bagi pemeriksaan ini.</p>
    @else
        <ul class="senarai-gambar-pemeriksaan">
            @foreach ($senaraiGambar as $gambar)
                @php
                    $urlLihat = route('pemeriksaan.gambar.lihat', [$rekod, $gambar]);
                    $urlMuatTurun = route('pemeriksaan.gambar.muat-turun', [$rekod, $gambar]);
                @endphp

                <li class="baris-gambar-pemeriksaan">
                    <button type="button" class="thumbnail-gambar-pemeriksaan" data-lihat-gambar-pemeriksaan data-src="{{ $urlLihat }}" data-muat-turun="{{ $urlMuatTurun }}" data-nama="{{ $gambar->asmi_name }}" data-ringkasan="{{ $gambar->asmi_summary }}" aria-label="Lihat {{ $gambar->asmi_name }}">
                        <img src="{{ $urlLihat }}" alt="{{ $gambar->asmi_name }}" loading="lazy">
                    </button>

                    <div class="teks-gambar-pemeriksaan">
                        <strong>{{ $gambar->asmi_name }}</strong>

                        @if (filled($gambar->asmi_summary))
                            <span>{{ $gambar->asmi_summary }}</span>
                        @endif
                    </div>

                    <div class="tindakan-gambar-pemeriksaan">
                        <button type="button" class="btn btn-sm btn-light" data-lihat-gambar-pemeriksaan data-src="{{ $urlLihat }}" data-muat-turun="{{ $urlMuatTurun }}" data-nama="{{ $gambar->asmi_name }}" data-ringkasan="{{ $gambar->asmi_summary }}" title="Lihat">
                            <i class="bi bi-eye" aria-hidden="true"></i><span class="d-none d-md-inline ms-1">Lihat</span>
                        </button>

                        <a href="{{ $urlMuatTurun }}" class="btn btn-sm btn-light" title="Muat turun">
                            <i class="bi bi-download" aria-hidden="true"></i><span class="d-none d-md-inline ms-1">Muat Turun</span>
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
