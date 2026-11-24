{{-- Pilihan muat turun aset. $jenis = bangunan|tanah, $aset = model --}}
<li><h6 class="dropdown-header">Muat turun</h6></li>
<li><a class="dropdown-item" href="{{ route($jenis . '.pdf', $aset) }}" data-muat-turun-berat><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>Maklumat aset (PDF)</a></li>
<li><a class="dropdown-item" href="{{ route($jenis . '.zip', $aset) }}" data-muat-turun-berat><i class="bi bi-file-earmark-zip" aria-hidden="true"></i>Maklumat + dokumen asal (ZIP)</a></li>
@if ($jenis === 'bangunan' && ($bolehMuatTurunPemeriksaan ?? false))
<li><a class="dropdown-item" href="{{ route('bangunan.pdf-pemeriksaan', $aset) }}" data-muat-turun-berat><i class="bi bi-clipboard2-check" aria-hidden="true"></i>Maklumat pemeriksaan (PDF)</a></li>
@endif
