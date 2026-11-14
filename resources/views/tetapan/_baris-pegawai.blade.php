<button type="button" class="baris-tetapan-medan baris-pegawai-apl" data-bs-toggle="modal" data-bs-target="#modalPegawai{{ $item->pgw_id }}">
    <span class="avatar-apl">{{ $item->inisial() }}</span>

    <span class="teks-baris-laporan">
        <strong>{{ $item->pgw_nama }}</strong>
        <small>{{ $item->pgw_jawatan }}</small>
    </span>

    <span class="lencana-peranan-apl">{{ \App\Support\Paparan::label($item->pgw_peranan) }}</span>
    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
</button>
