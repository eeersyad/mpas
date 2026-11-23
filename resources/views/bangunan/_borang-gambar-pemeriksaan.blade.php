@php
    $gambarSediaAda = $pemeriksaanTerkini?->gambar ?? collect();
    $gambarDipadam = array_map('intval', (array) old('pemeriksaan.padam_gambar', []));
    $gambarBaruLama = old('pemeriksaan.gambar', []);
@endphp

{{-- Gambar pemeriksaan --}}
<div class="panel-pemeriksaan mb-3" data-borang-gambar-pemeriksaan data-url-muat-naik="{{ route('pemeriksaan.gambar.simpan-sementara') }}">
    <div class="kepala-gambar-pemeriksaan">
        <span class="form-label small fw-semibold mb-0">
            <i class="bi bi-images me-1" aria-hidden="true"></i>Gambar Pemeriksaan
            <span class="text-secondary fw-normal">(pilihan)</span>
        </span>

        <button type="button" class="btn btn-sm btn-outline-primary" data-tambah-gambar-borang>
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah Gambar
        </button>
    </div>

    @error('pemeriksaan.gambar.*')
        <div class="alert alert-danger small py-2 mb-2">{{ $message }}</div>
    @enderror

    {{-- Gambar sedia ada (hanya ketika kemas kini rekod terkini) --}}
    @if ($gambarSediaAda->isNotEmpty())
        <fieldset data-gambar-sedia-ada @class(['d-none' => $modPemeriksaan !== 'KEMASKINI']) @disabled($modPemeriksaan !== 'KEMASKINI')>
            <ul class="senarai-gambar-pemeriksaan mb-2" data-gambar-pemeriksaan="sedia-ada">
                @foreach ($gambarSediaAda as $gambar)
                    @php
                        $urlLihat = route('pemeriksaan.gambar.lihat', [$pemeriksaanTerkini, $gambar]);
                        $dipadam = in_array($gambar->asmi_id, $gambarDipadam, true);
                    @endphp

                    <li @class(['baris-gambar-pemeriksaan', 'akan-dipadam' => $dipadam]) data-baris-gambar-sedia-ada>
                        <button type="button" class="thumbnail-gambar-pemeriksaan" data-lihat-gambar-pemeriksaan data-src="{{ $urlLihat }}" data-muat-turun="{{ route('pemeriksaan.gambar.muat-turun', [$pemeriksaanTerkini, $gambar]) }}" data-nama="{{ $gambar->asmi_name }}" data-ringkasan="{{ $gambar->asmi_summary }}" aria-label="Lihat {{ $gambar->asmi_name }}">
                            <img src="{{ $urlLihat }}" alt="{{ $gambar->asmi_name }}" loading="lazy">
                        </button>

                        <div class="teks-gambar-pemeriksaan">
                            <strong>{{ $gambar->asmi_name }}</strong>
                            @if (filled($gambar->asmi_summary))
                                <span>{{ $gambar->asmi_summary }}</span>
                            @endif
                            <em class="label-akan-dipadam">Akan dipadam apabila disimpan</em>
                        </div>

                        <input type="hidden" name="pemeriksaan[padam_gambar][]" value="{{ $gambar->asmi_id }}" data-input-padam @disabled(! $dipadam)>

                        <div class="tindakan-gambar-pemeriksaan">
                            <button type="button" class="btn btn-sm btn-light text-danger" data-togol-padam-gambar title="Padam">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light" data-togol-padam-gambar title="Batal padam">
                                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        </fieldset>
    @endif

    {{-- Gambar baharu (dimuat naik sementara; dipautkan apabila borang disimpan) --}}
    <ul class="senarai-gambar-baru" data-senarai-gambar-baru>
        @foreach ($gambarBaruLama as $indeks => $baris)
            @if (filled($baris['token'] ?? null))
                <li class="baris-gambar-baru" data-baris-gambar-baru>
                    <img src="{{ route('pemeriksaan.gambar.sementara', $baris['token']) }}" alt="">
                    <div class="borang-gambar-medan">
                        <input type="hidden" name="pemeriksaan[gambar][{{ $indeks }}][token]" value="{{ $baris['token'] }}">
                        <input type="text" name="pemeriksaan[gambar][{{ $indeks }}][nama]" value="{{ $baris['nama'] ?? '' }}" maxlength="150" placeholder="Nama gambar *" required class="form-control form-control-sm @error("pemeriksaan.gambar.{$indeks}.nama") is-invalid @enderror">
                        <textarea name="pemeriksaan[gambar][{{ $indeks }}][ringkasan]" rows="2" maxlength="1000" placeholder="Ringkasan (pilihan)" class="form-control form-control-sm">{{ $baris['ringkasan'] ?? '' }}</textarea>
                    </div>
                    <button type="button" class="btn btn-sm btn-light text-danger" data-buang-gambar-baru title="Buang"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                </li>
            @endif
        @endforeach
    </ul>

    <p @class(['text-secondary small mb-0', 'd-none' => ($gambarSediaAda->isNotEmpty() && $modPemeriksaan === 'KEMASKINI') || ! empty($gambarBaruLama)]) data-teks-tiada-gambar>
        Tiada gambar. Klik <strong>Tambah Gambar</strong> untuk memuat naik.
    </p>
</div>
