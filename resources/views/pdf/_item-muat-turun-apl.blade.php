{{-- Item menu (telefon) yang membuka bottom sheet muat turun. $jenis = bangunan|tanah, $aset = model --}}
<li><button type="button" class="dropdown-item" data-lembaran-muat-turun data-pdf="{{ route($jenis . '.pdf', $aset) }}" data-zip="{{ route($jenis . '.zip', $aset) }}"><i class="bi bi-download" aria-hidden="true"></i>Muat turun</button></li>
