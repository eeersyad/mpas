{{--
    Penapis gaya aplikasi untuk telefon (< 768px):
    bar carian + butang Tapis, cip penapis aktif, dan bottom sheet (offcanvas).

    Guna:
    <x-penapis-mobile placeholder="Cari..." :penapis="$penapisMobile" :susun="[...]" :url-semula="route('...')" />

    $penapisMobile = [
        'status_premis' => ['label' => 'Status Premis', 'pilihan' => ['SEWA', 'PAJAK']],             // label dijana
        'jenis_pengadu' => ['label' => 'Pengadu', 'pilihan' => ['PENYEWA' => 'Penyewa', ...]],     // label sendiri
    ];
--}}
@props([
    'placeholder' => 'Cari...',
    'penapis' => [],
    'susun' => [],
    'urlSemula' => null,
])

@php
    $urlSemasa = url()->current();
    $urlSemula = $urlSemula ?? $urlSemasa;

    // Pilihan dalam bentuk senarai (['SEWA', ...]) ditukar kepada ['SEWA' => 'Sewa', ...]
    $penapis = collect($penapis)->map(function ($kumpulan) {
        $pilihan = $kumpulan['pilihan'] ?? [];

        if (array_is_list($pilihan)) {
            $pilihan = collect($pilihan)
                ->mapWithKeys(fn ($nilai) => [$nilai => \App\Support\Paparan::label($nilai)])
                ->all();
        }

        return [...$kumpulan, 'pilihan' => $pilihan];
    })->all();

    $sortSemasa = request('sort');
    $arahSemasa = request('direction', 'desc');
    $nilaiSusunSemasa = $sortSemasa ? "{$sortSemasa}|{$arahSemasa}" : '';
    $ikonArah = ['asc' => '↑', 'desc' => '↓'];

    $penapisAktif = [];

    foreach ($penapis as $nama => $kumpulan) {
        // Kumpulan julat tarikh: ['jenis' => 'tarikh', 'medan' => ['tarikh_mula' => 'Dari', ...]]
        if (($kumpulan['jenis'] ?? null) === 'tarikh') {
            foreach ($kumpulan['medan'] ?? [] as $namaMedan => $labelMedan) {
                if (filled(request($namaMedan))) {
                    $penapisAktif[$namaMedan] = $labelMedan . ' ' . \Illuminate\Support\Carbon::parse(request($namaMedan))->format('d/m/Y');
                }
            }

            continue;
        }

        $nilai = request($nama);

        if (filled($nilai) && isset($kumpulan['pilihan'][$nilai])) {
            $penapisAktif[$nama] = $kumpulan['pilihan'][$nilai];
        }
    }

    $susunAktif = $sortSemasa && isset($susun[$sortSemasa]);
    $bilanganAktif = count($penapisAktif) + ($susunAktif ? 1 : 0);

    $urlTanpa = function (array $buang) use ($urlSemasa) {
        $parameter = request()->except([...$buang, 'page']);

        return $urlSemasa . ($parameter ? '?' . http_build_query($parameter) : '');
    };
@endphp

<div class="penapis-mobile d-md-none mb-3">
    {{-- 1. Bar carian + butang Tapis --}}
    <form method="GET" action="{{ $urlSemasa }}" class="bar-penapis-mobile" role="search">
        @foreach (request()->except(['cari', 'page']) as $kunci => $nilai)
            @if (is_scalar($nilai) && filled($nilai))
                <input type="hidden" name="{{ $kunci }}" value="{{ $nilai }}">
            @endif
        @endforeach

        <div class="carian-penapis-mobile">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="{{ $placeholder }}" enterkeyhint="search" aria-label="Carian">
        </div>

        <button type="button" class="btn butang-tapis-mobile {{ $bilanganAktif ? 'aktif' : '' }}" data-bs-toggle="offcanvas" data-bs-target="#penapisMobile" aria-controls="penapisMobile">
            <i class="bi bi-sliders2" aria-hidden="true"></i>
            <span>Tapis</span>

            @if ($bilanganAktif)
                <span class="bilangan-tapis">{{ $bilanganAktif }}</span>
            @endif
        </button>
    </form>

    {{-- 2. Cip penapis aktif --}}
    @if ($bilanganAktif)
        <div class="cip-penapis-mobile">
            @if ($susunAktif)
                <a href="{{ $urlTanpa(['sort', 'direction']) }}" class="cip-penapis" aria-label="Buang susunan">
                    <i class="bi bi-arrow-down-up" aria-hidden="true"></i>
                    {{ $susun[$sortSemasa] }} {{ $ikonArah[$arahSemasa] ?? '' }}
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </a>
            @endif

            @foreach ($penapisAktif as $nama => $label)
                <a href="{{ $urlTanpa([$nama]) }}" class="cip-penapis" aria-label="Buang penapis {{ $label }}">
                    {{ $label }}
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </a>
            @endforeach

            <a href="{{ $urlSemula }}" class="cip-kosongkan">Kosongkan</a>
        </div>
    @endif
</div>

{{-- 3. Bottom sheet: diletakkan DI LUAR bar melekat. Bar menggunakan backdrop-filter,
       yang menyebabkan elemen position:fixed di dalamnya terperangkap dalam bar. --}}
<div class="d-md-none">
    <div class="offcanvas offcanvas-bottom panel-penapis-mobile" tabindex="-1" id="penapisMobile" aria-labelledby="penapisMobileTajuk">
        <div class="pemegang-panel" aria-hidden="true"></div>

        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="penapisMobileTajuk">Tapis &amp; Susun</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>

        <form method="GET" action="{{ $urlSemasa }}" class="borang-penapis-mobile" data-penapis-mobile-borang>
            <input type="hidden" name="cari" value="{{ request('cari') }}">
            <input type="hidden" name="had" value="{{ request('had') }}">
            <input type="hidden" name="sort" value="{{ $sortSemasa }}" data-susun-sort>
            <input type="hidden" name="direction" value="{{ $sortSemasa ? $arahSemasa : '' }}" data-susun-arah>

            <div class="offcanvas-body">
                @if ($susun)
                    <fieldset class="kumpulan-pill">
                        <legend>Susun ikut</legend>

                        <div class="senarai-pill">
                            <input type="radio" class="btn-check" name="_susun" id="pmSusunTerkini" value="" autocomplete="off" @checked($nilaiSusunSemasa === '')>
                            <label class="pill-pilihan" for="pmSusunTerkini">Terkini</label>

                            @foreach ($susun as $medan => $label)
                                @foreach (['asc', 'desc'] as $arah)
                                    <input type="radio" class="btn-check" name="_susun" id="pmSusun-{{ $medan }}-{{ $arah }}" value="{{ $medan }}|{{ $arah }}" autocomplete="off" @checked($nilaiSusunSemasa === "{$medan}|{$arah}")>
                                    <label class="pill-pilihan" for="pmSusun-{{ $medan }}-{{ $arah }}">{{ $label }} {{ $ikonArah[$arah] }}</label>
                                @endforeach
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                @foreach ($penapis as $nama => $kumpulan)
                    @if (($kumpulan['jenis'] ?? null) === 'tarikh')
                        <fieldset class="kumpulan-pill">
                            <legend>{{ $kumpulan['label'] }}</legend>

                            <div class="row g-2">
                                @foreach ($kumpulan['medan'] ?? [] as $namaMedan => $labelMedan)
                                    <div class="col-6">
                                        <label for="pm-{{ $namaMedan }}" class="label-tarikh-pill">{{ $labelMedan }}</label>
                                        <input type="date" id="pm-{{ $namaMedan }}" name="{{ $namaMedan }}" value="{{ request($namaMedan) }}" class="form-control medan-tarikh-pill">
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        @continue
                    @endif

                    <fieldset class="kumpulan-pill">
                        <legend>{{ $kumpulan['label'] }}</legend>

                        <div class="senarai-pill">
                            <input type="radio" class="btn-check" name="{{ $nama }}" id="pm-{{ $nama }}-semua" value="" autocomplete="off" @checked(blank(request($nama)))>
                            <label class="pill-pilihan" for="pm-{{ $nama }}-semua">Semua</label>

                            @foreach ($kumpulan['pilihan'] as $nilai => $label)
                                <input type="radio" class="btn-check" name="{{ $nama }}" id="pm-{{ $nama }}-{{ $nilai }}" value="{{ $nilai }}" autocomplete="off" @checked(request($nama) === (string) $nilai)>
                                <label class="pill-pilihan" for="pm-{{ $nama }}-{{ $nilai }}">{{ $label }}</label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>

            <div class="kaki-penapis-mobile">
                <a href="{{ $urlSemula }}" class="btn btn-light">Set semula</a>
                <button type="submit" class="btn btn-utama flex-grow-1">Tunjuk keputusan</button>
            </div>
        </form>
    </div>
</div>
