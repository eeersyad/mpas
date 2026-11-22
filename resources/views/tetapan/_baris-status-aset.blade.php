<button type="button" class="baris-tetapan-medan baris-pegawai-apl" data-bs-toggle="modal" data-bs-target="#modalStatus{{ $item->sts_id }}">
    <span @class(['titik-warna-status', 'status-' . $item->sts_warna => ! $item->warnaSuai(), 'titik-warna-suai ada-warna' => $item->warnaSuai()]) style="{{ $item->gayaWarna() }}" aria-hidden="true"></span>

    <span class="teks-baris-laporan">
        <strong>{{ $item->sts_label }}</strong>
        <small>{{ (int) ($penggunaan[$item->sts_kod] ?? 0) }} rekod @if ($item->sts_boleh_iklan)· <i class="bi bi-megaphone-fill"></i> Boleh diiklankan @endif</small>
    </span>

    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
</button>
