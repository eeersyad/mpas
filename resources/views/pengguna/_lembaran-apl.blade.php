{{-- Bottom sheet peranan & kata laluan untuk borang pengguna (telefon) --}}
@php
    $peneranganPeranan = [
        'SYSTEM' => 'Akses penuh, termasuk pengguna dan tetapan',
        'BPH' => 'Aset bangunan, tanah, aduan, kod QR dan laporan',
        'BP' => 'Aset tanah dan laporan',
        'KJ' => 'Aduan dan laporan',
    ];
@endphp

@if (! empty($peranan))
<div class="offcanvas offcanvas-bottom panel-penapis-mobile d-md-none" tabindex="-1" id="lembaranPeranan" aria-labelledby="lembaranPerananTajuk">
    <div class="pemegang-panel" aria-hidden="true"></div>

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="lembaranPerananTajuk">Pilih Peranan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>

    <div class="offcanvas-body pt-0">
        <div class="kad-senarai-apl">
            @foreach ($peranan as $item)
                <button type="button" class="baris-tetapan-medan pilihan-apl" data-pilih-untuk="usr_level" data-nilai="{{ $item }}">
                    <span class="avatar-apl">{{ substr($item, 0, 3) }}</span>

                    <span class="teks-baris-laporan">
                        <strong>{{ $namaPeranan[$item] ?? $item }}</strong>
                        <small>{{ $peneranganPeranan[$item] ?? $item }}</small>
                    </span>

                    <i class="bi bi-check-lg tanda-pilih-apl" aria-hidden="true"></i>
                </button>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="offcanvas offcanvas-bottom panel-penapis-mobile d-md-none" tabindex="-1" id="lembaranKataLaluan" aria-labelledby="lembaranKataLaluanTajuk" data-lembaran-kata-laluan data-sasaran="usr_password" data-sasaran-sahkan="usr_password_confirmation" data-wajib="{{ $wajib ? '1' : '' }}" @if (! empty($semasa)) data-sasaran-semasa="kata_laluan_semasa" @endif>
    <div class="pemegang-panel" aria-hidden="true"></div>

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="lembaranKataLaluanTajuk">{{ $wajib ? 'Kata Laluan' : 'Tukar Kata Laluan' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>

    <div class="offcanvas-body pt-0">
        @if (! empty($semasa))
            <label for="klAplSemasa" class="label-tarikh-pill">Kata laluan semasa</label>
            <div class="input-kata-laluan mb-3">
                <input type="password" class="form-control medan-sunting-apl" id="klAplSemasa" autocomplete="current-password" data-kl-semasa>
                <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#klAplSemasa" aria-label="Papar kata laluan" title="Papar kata laluan"><i class="bi bi-eye"></i></button>
            </div>
        @endif

        <label for="klAplBaru" class="label-tarikh-pill">Kata laluan baharu</label>
        <div class="input-kata-laluan mb-3">
            <input type="password" class="form-control medan-sunting-apl" id="klAplBaru" autocomplete="new-password" data-kl-baru>
            <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#klAplBaru" aria-label="Papar kata laluan" title="Papar kata laluan"><i class="bi bi-eye"></i></button>
        </div>

        <label for="klAplSahkan" class="label-tarikh-pill">Sahkan kata laluan</label>
        <div class="input-kata-laluan">
            <input type="password" class="form-control medan-sunting-apl" id="klAplSahkan" autocomplete="new-password" data-kl-sahkan>
            <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#klAplSahkan" aria-label="Papar kata laluan" title="Papar kata laluan"><i class="bi bi-eye"></i></button>
        </div>

        <p class="petunjuk-kl-apl" data-kl-petunjuk>Min. 8 aksara, mengandungi huruf besar, huruf kecil dan nombor.</p>

        <div class="d-flex gap-2 mt-3">
            @unless ($wajib)
                <button type="button" class="btn btn-light" data-kl-kosongkan>Jangan tukar</button>
            @endunless
            <button type="button" class="btn btn-utama flex-grow-1" data-kl-simpan>Selesai</button>
        </div>
    </div>
</div>
