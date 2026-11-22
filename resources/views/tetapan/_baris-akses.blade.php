<button type="button" class="baris-tetapan-medan baris-pegawai-apl" data-bs-toggle="modal" data-bs-target="#modalAkses{{ $item->tap_id }}">
    <span class="avatar-apl">{{ mb_strtoupper(mb_substr($item->pengguna->usr_name, 0, 2)) }}</span>

    <span class="teks-baris-laporan">
        <strong>{{ $item->pengguna->usr_name }}</strong>
        <small>{{ $item->pengguna->usr_level }}</small>
    </span>

    <span class="lencana-peranan-apl">{{ $item->labelTahap() }}</span>
    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
</button>
