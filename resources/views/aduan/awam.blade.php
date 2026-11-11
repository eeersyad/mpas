@extends('layouts.awam')

@section('tajuk', 'Aduan Aset Bangunan')

@section('kandungan')
    @php
        $bangunan = $kodBangunan->bangunan;
        $jumlahPenyewa = $bangunan->penyewa->count();

        $status = [
            'BARU' => ['label' => 'Aduan Diterima', 'peratus' => 25, 'warna' => 'primary'],
            'DITERIMA' => ['label' => 'Dalam Tindakan BPH', 'peratus' => 50, 'warna' => 'info'],
            'DALAM_PENYELENGGARAAN' => ['label' => 'Dalam Penyelenggaraan', 'peratus' => 75, 'warna' => 'warning'],
            'SELESAI' => ['label' => 'Selesai', 'peratus' => 100, 'warna' => 'success'],
        ];
    @endphp

    <header class="awam-hero mb-4">
        <div class="awam-logo mb-3">
            <img
                src="{{ asset('images/logo-mpas-2.png') }}"
                alt="Logo MPAS"
            >
        </div>

        <div class="maklumat-aset-awam">
            <h1>{{ $bangunan->asb_name }}</h1>

            <span class="jumlah-penyewa-awam">
                <i class="bi bi-people-fill"></i>

                @if ($jumlahPenyewa > 0)
                    {{ $jumlahPenyewa }} Penyewa
                @else
                    Tiada Penyewa
                @endif
            </span>

            @php
                $alamatAsetAwam = collect([
                    $bangunan->asb_add1,
                    $bangunan->asb_add2,
                    $bangunan->asb_postcode,
                    $bangunan->asb_distinct,
                    $bangunan->asb_state,
                ])->filter()->implode(', ');
            @endphp

            <p class="alamat-aset-awam">
                <i class="bi bi-geo-alt-fill"></i>
                <span>{{ $alamatAsetAwam ?: 'Alamat aset belum direkodkan.' }}</span>
            </p>
        </div>
    </header>

    @if ($progressAduan->isNotEmpty())
        <section class="kad-awam mb-4 rekod-aduan-awam">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <p class="teks-kecil mb-1">STATUS ADUAN ASET</p>
                    <h2 class="h6 fw-bold mb-1">
                        <i class="bi bi-activity me-2 text-success"></i>
                        Rekod Aduan Semasa
                    </h2>
                    <p class="text-secondary small mb-0 penerangan-rekod-aduan">
                        Aduan terbaru dipaparkan di hadapan.
                    </p>
                </div>

                <span class="jumlah-rekod-aduan">
                    {{ $progressAduan->count() }} rekod
                </span>
            </div>

            <div class="gelangsar-aduan" data-gelangsar-aduan>
                <div class="dek-aduan" data-dek-aduan>
                    @foreach ($progressAduan as $item)
                        @php
                            $maklumatStatus = $status[$item->cmp_status];
                            $kelasKad = match ($loop->iteration) {
                                1 => 'aktif',
                                2 => 'kedua',
                                3 => 'ketiga',
                                default => 'tersembunyi',
                            };
                        @endphp

                        <article
                            class="kad-susun-aduan {{ $kelasKad }}"
                            data-kad-aduan
                            data-indeks="{{ $loop->index }}"
                            aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                        >
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="kad-aduan-ikon {{ strtolower($item->cmp_status) }}">
                                    <i class="bi bi-tools"></i>
                                </div>

                                <span class="badge text-bg-{{ $maklumatStatus['warna'] }}">
                                    {{ $maklumatStatus['label'] }}
                                </span>
                            </div>

                            <div class="mt-3">
                                <strong class="d-block tajuk-kad-aduan">
                                    {{ $item->cmp_damage_type }}
                                </strong>

                                <p class="text-secondary small mb-2">
                                    Aduan direkodkan pada
                                    {{ $item->cmp_received_date?->format('d/m/Y, h:i A') }}
                                </p>

                                @if ($item->cmp_note)
                                    <p class="nota-kad-aduan mb-3">
                                        {{ $item->cmp_note }}
                                    </p>
                                @endif

                                <div class="progress" role="progressbar">
                                    <div
                                        class="progress-bar bg-{{ $maklumatStatus['warna'] }}"
                                        style="width: {{ $maklumatStatus['peratus'] }}%"
                                    ></div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($progressAduan->count() > 1)
                    <div class="kawalan-gelongsar-aduan">
                        <button
                            type="button"
                            class="butang-gelongsar-aduan"
                            data-sebelum-aduan
                            aria-label="Lihat aduan sebelumnya"
                        >
                            <i class="bi bi-arrow-left"></i>
                        </button>

                        <div class="penunjuk-gelongsar-aduan" data-penunjuk-aduan>
                            @foreach ($progressAduan as $item)
                                <button
                                    type="button"
                                    class="titik-gelongsar-aduan {{ $loop->first ? 'aktif' : '' }}"
                                    data-pilih-aduan="{{ $loop->index }}"
                                    aria-label="Lihat aduan {{ $loop->iteration }}"
                                ></button>
                            @endforeach
                        </div>

                        <button
                            type="button"
                            class="butang-gelongsar-aduan"
                            data-seterusnya-aduan
                            aria-label="Lihat aduan seterusnya"
                        >
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="kad-awam borang-awam">
        <div class="kepala-borang-awam mb-4">
            <span class="ikon-borang-awam">
                <i class="bi bi-pencil-square"></i>
            </span>

            <div>
                <p class="teks-kecil mb-1">BORANG ADUAN</p>
                <h2 class="h5 fw-bold mb-1">Buat Aduan Baharu</h2>
                <p class="text-secondary small mb-0">
                    Lengkapkan maklumat kerosakan untuk tindakan pihak MDM.
                </p>
            </div>
        </div>

        <form
            id="borangAduanAwam"
            method="POST"
            action="{{ route('aduan.awam.store', $kodBangunan->qr_code_value) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="mb-3">
                <label for="jenis_pengadu" class="form-label fw-semibold">
                    Kategori Pengadu <span class="text-danger">*</span>
                </label>

                <select
                    id="jenis_pengadu"
                    name="jenis_pengadu"
                    class="form-select @error('jenis_pengadu') is-invalid @enderror"
                    required
                >
                    <option value="">-- Pilih Kategori --</option>
                    <option value="PENYEWA" @selected(old('jenis_pengadu') === 'PENYEWA')>
                        Penyewa
                    </option>
                    <option value="ORANG_AWAM" @selected(old('jenis_pengadu') === 'ORANG_AWAM')>
                        Orang Awam
                    </option>
                </select>

                @error('jenis_pengadu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="ruangPenyewa" class="mb-3" hidden>
                <label for="tnt_id" class="form-label fw-semibold">
                    Nama Penyewa <span class="text-danger">*</span>
                </label>

                <select
                    id="tnt_id"
                    name="tnt_id"
                    class="form-select @error('tnt_id') is-invalid @enderror"
                >
                    <option value="">-- Pilih Nama Penyewa --</option>

                    @foreach ($bangunan->penyewa as $penyewa)
                        <option
                            value="{{ $penyewa->tnt_id }}"
                            @selected(old('tnt_id') == $penyewa->tnt_id)
                        >
                            {{ $penyewa->tnt_name }}
                        </option>
                    @endforeach
                </select>

                @error('tnt_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="ruangOrangAwam" class="mb-3" hidden>
                <label for="cmp_reporter_name" class="form-label fw-semibold">
                    Nama Pengadu <span class="text-danger">*</span>
                </label>

                <input
                    id="cmp_reporter_name"
                    type="text"
                    name="cmp_reporter_name"
                    class="form-control @error('cmp_reporter_name') is-invalid @enderror"
                    value="{{ old('cmp_reporter_name') }}"
                    placeholder="Masukkan nama penuh anda"
                >

                <div class="mt-3">
                    <label for="cmp_reporter_phone" class="form-label fw-semibold">
                        Nombor Telefon <span class="text-danger">*</span>
                    </label>

                    <input
                        id="cmp_reporter_phone"
                        type="tel"
                        name="cmp_reporter_phone"
                        class="form-control @error('cmp_reporter_phone') is-invalid @enderror"
                        value="{{ old('cmp_reporter_phone') }}"
                        inputmode="tel"
                        autocomplete="tel"
                        placeholder="Contoh: 012-3456789"
                    >

                    @error('cmp_reporter_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @error('cmp_reporter_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cmp_reporter_email" class="form-label fw-semibold">
                    Alamat Emel <span class="text-danger">*</span>
                </label>

                <input id="cmp_reporter_email" type="email" name="cmp_reporter_email" class="form-control @error('cmp_reporter_email') is-invalid @enderror" value="{{ old('cmp_reporter_email') }}" inputmode="email" autocomplete="email" maxlength="150" placeholder="Contoh: nama@email.com" required>

                <div class="form-text">
                    <i class="bi bi-envelope-check me-1"></i>
                    No. rujukan dan makluman status aduan akan dihantar ke emel ini. Emel anda tidak digunakan untuk tujuan lain.
                </div>

                @error('cmp_reporter_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cmp_damage_type" class="form-label fw-semibold">
                    Jenis Kerosakan <span class="text-danger">*</span>
                </label>

                <select
                    id="cmp_damage_type"
                    name="cmp_damage_type"
                    class="form-select @error('cmp_damage_type') is-invalid @enderror"
                    required
                >
                    <option value="">-- Pilih Jenis Kerosakan --</option>

                    @foreach ([
                        'STRUKTUR BANGUNAN',
                        'ELEKTRIK',
                        'PAIP DAN SANITARI',
                        'BUMBUNG',
                        'PINTU ATAU TINGKAP',
                        'KEBERSIHAN',
                        'LAIN-LAIN',
                    ] as $jenis)
                        <option
                            value="{{ $jenis }}"
                            @selected(old('cmp_damage_type') === $jenis)
                        >
                            {{ ucwords(strtolower($jenis)) }}
                        </option>
                    @endforeach
                </select>

                @error('cmp_damage_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cmp_note" class="form-label fw-semibold">
                    Keterangan Aduan <span class="text-danger">*</span>
                </label>

                <textarea
                    id="cmp_note"
                    name="cmp_note"
                    rows="5"
                    class="form-control @error('cmp_note') is-invalid @enderror"
                    placeholder="Terangkan kerosakan yang berlaku dan lokasi kerosakan."
                    required
                >{{ old('cmp_note') }}</textarea>

                @error('cmp_note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4" data-pilihan-gambar-aduan>
                <label class="form-label fw-semibold">
                    Gambar Kerosakan
                </label>

                <div class="pilihan-gambar-awam">
                    <button
                        type="button"
                        class="butang-pilih-gambar"
                        data-buka-galeri
                    >
                        <i class="bi bi-images"></i>

                        <span>
                            <strong>Pilih dari Galeri</strong>
                            <small>Pilih sehingga 4 gambar</small>
                        </span>
                    </button>

                    <button
                        type="button"
                        class="butang-pilih-gambar"
                        data-buka-kamera
                    >
                        <i class="bi bi-camera-fill"></i>

                        <span>
                            <strong>Ambil Gambar</strong>
                            <small>Guna kamera telefon</small>
                        </span>
                    </button>
                </div>

                <input
                    id="gambarGaleri"
                    type="file"
                    name="gambar[]"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    class="visually-hidden"
                    data-gambar-galeri
                >

                <div data-input-kamera></div>

                <div class="form-text" data-status-gambar>
                    0 daripada 4 gambar dipilih. Saiz setiap gambar tidak melebihi 5 MB.
                </div>

                <div
                    class="preview-gambar-awam"
                    data-preview-gambar
                    aria-live="polite"
                ></div>

                @error('gambar')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror

                @error('gambar.*')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-utama butang-hantar-aduan w-100 py-3">
                <i class="bi bi-send-fill me-2"></i>
                Hantar Aduan
            </button>

            <p class="text-center small text-secondary mt-3 mb-0">
                Sudah membuat aduan? <a href="{{ route('aduan.status') }}">Semak status aduan</a>
            </p>
        </form>
    </section>

    <p class="footer-awam text-center small mt-4 mb-0">
        <i class="bi bi-shield-check me-1"></i>
        MPAS · Sistem Aset Tak Alih Majlis Daerah Marang
    </p>

    <script>
        const kategoriPengadu = document.querySelector('#jenis_pengadu');
        const ruangPenyewa = document.querySelector('#ruangPenyewa');
        const pilihanPenyewa = document.querySelector('#tnt_id');
        const ruangOrangAwam = document.querySelector('#ruangOrangAwam');
        const namaPengaduAwam = document.querySelector('#cmp_reporter_name');
        const telefonPengaduAwam = document.querySelector('#cmp_reporter_phone');

        const kemaskiniPengadu = () => {
            const penyewa = kategoriPengadu.value === 'PENYEWA';
            const orangAwam = kategoriPengadu.value === 'ORANG_AWAM';

            ruangPenyewa.hidden = !penyewa;
            pilihanPenyewa.required = penyewa;

            ruangOrangAwam.hidden = !orangAwam;
            namaPengaduAwam.required = orangAwam;
            telefonPengaduAwam.required = orangAwam;

            if (!penyewa) {
                pilihanPenyewa.value = '';
            }

            if (!orangAwam) {
                namaPengaduAwam.value = '';
                telefonPengaduAwam.value = '';
            }
        };

        kategoriPengadu.addEventListener('change', kemaskiniPengadu);
        kemaskiniPengadu();

        const panelGambarAduan = document.querySelector('[data-pilihan-gambar-aduan]');

        if (panelGambarAduan) {
            const hadGambar = 4;
            const inputGaleri = panelGambarAduan.querySelector('[data-gambar-galeri]');
            const bekasInputKamera = panelGambarAduan.querySelector('[data-input-kamera]');
            const butangGaleri = panelGambarAduan.querySelector('[data-buka-galeri]');
            const butangKamera = panelGambarAduan.querySelector('[data-buka-kamera]');
            const statusGambar = panelGambarAduan.querySelector('[data-status-gambar]');
            const previewGambar = panelGambarAduan.querySelector('[data-preview-gambar]');

            const dapatGambarGaleri = () =>
                [...(inputGaleri.files ?? [])].map((fail, indeks) => ({
                    sumber: 'galeri',
                    id: `galeri-${indeks}`,
                    indeks,
                    fail,
                }));

            const dapatGambarKamera = () =>
                [...bekasInputKamera.querySelectorAll('[data-gambar-kamera]')]
                    .filter((input) => input.files?.length > 0)
                    .map((input) => ({
                        sumber: 'kamera',
                        id: input.dataset.id,
                        fail: input.files[0],
                    }));

            const semuaGambar = () => [
                ...dapatGambarGaleri(),
                ...dapatGambarKamera(),
            ];

            const jumlahGambarDipilih = () => semuaGambar().length;

            const paparRalatHadGambar = () => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Had gambar telah dicapai',
                    text: 'Maksimum empat gambar sahaja boleh dimuat naik bagi satu aduan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#168A7E',
                });
            };

            const kemaskiniFailGaleri = (fail) => {
                const pemindahanFail = new DataTransfer();

                fail.forEach((item) => {
                    pemindahanFail.items.add(item);
                });

                inputGaleri.files = pemindahanFail.files;
            };

            const padamGambar = (gambar) => {
                if (gambar.sumber === 'galeri') {
                    const failGaleri = [...inputGaleri.files];
                    failGaleri.splice(gambar.indeks, 1);
                    kemaskiniFailGaleri(failGaleri);
                } else {
                    bekasInputKamera
                        .querySelector(`[data-gambar-kamera][data-id="${gambar.id}"]`)
                        ?.remove();
                }

                kemaskiniPaparanGambar();
            };

            const kemaskiniPreviewGambar = () => {
                previewGambar.replaceChildren();

                semuaGambar().forEach((gambar, indeks) => {
                    const kadPreview = document.createElement('div');
                    kadPreview.className = 'item-preview-gambar';

                    const imej = document.createElement('img');
                    imej.src = URL.createObjectURL(gambar.fail);
                    imej.alt = `Gambar kerosakan ${indeks + 1}`;

                    const butangPadam = document.createElement('button');
                    butangPadam.type = 'button';
                    butangPadam.className = 'butang-buang-preview';
                    butangPadam.title = `Buang gambar ${indeks + 1}`;
                    butangPadam.setAttribute('aria-label', `Buang gambar ${indeks + 1}`);
                    butangPadam.innerHTML = '<i class="bi bi-x-lg"></i>';

                    butangPadam.addEventListener('click', () => {
                        padamGambar(gambar);
                    });

                    kadPreview.append(imej, butangPadam);
                    previewGambar.appendChild(kadPreview);
                });
            };

            const kemaskiniPaparanGambar = () => {
                const jumlah = jumlahGambarDipilih();

                statusGambar.textContent =
                    `${jumlah} daripada ${hadGambar} gambar dipilih. ` +
                    'Saiz setiap gambar tidak melebihi 5 MB.';

                butangKamera.disabled = jumlah >= hadGambar;
                butangKamera.classList.toggle('disabled', jumlah >= hadGambar);

                kemaskiniPreviewGambar();
            };

            butangGaleri.addEventListener('click', () => {
                inputGaleri.click();
            });

            inputGaleri.addEventListener('change', () => {
                const jumlahKamera = dapatGambarKamera().length;
                const bakiGambar = hadGambar - jumlahKamera;
                const failDipilih = [...inputGaleri.files];

                if (failDipilih.length > bakiGambar) {
                    kemaskiniFailGaleri(failDipilih.slice(0, Math.max(0, bakiGambar)));
                    paparRalatHadGambar();
                }

                kemaskiniPaparanGambar();
            });

            butangKamera.addEventListener('click', () => {
                if (jumlahGambarDipilih() >= hadGambar) {
                    paparRalatHadGambar();
                    return;
                }

                const inputKamera = document.createElement('input');

                inputKamera.type = 'file';
                inputKamera.name = 'gambar[]';
                inputKamera.accept = 'image/jpeg,image/png,image/webp';
                inputKamera.capture = 'environment';
                inputKamera.hidden = true;
                inputKamera.dataset.gambarKamera = 'ya';
                inputKamera.dataset.id =
                    window.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random()}`;

                inputKamera.addEventListener('change', () => {
                    if (!inputKamera.files?.length) {
                        inputKamera.remove();
                    } else if (jumlahGambarDipilih() > hadGambar) {
                        inputKamera.remove();
                        paparRalatHadGambar();
                    }

                    kemaskiniPaparanGambar();
                });

                bekasInputKamera.appendChild(inputKamera);
                inputKamera.click();
            });

            kemaskiniPaparanGambar();
        }

        const borangAduanAwam = document.querySelector('#borangAduanAwam');

        borangAduanAwam.addEventListener('submit', (event) => {
            if (borangAduanAwam.dataset.disahkan) {
                return;
            }

            event.preventDefault();

            Swal.fire({
                icon: 'question',
                title: 'Hantar aduan?',
                text: 'Pastikan maklumat dan gambar yang dimasukkan adalah betul.',
                showCancelButton: true,
                confirmButtonText: 'Ya, hantar',
                cancelButtonText: 'Semak semula',
                confirmButtonColor: '#168A7E',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    borangAduanAwam.dataset.disahkan = 'ya';
                    borangAduanAwam.submit();
                }
            });
        });

        const gelangsarAduan = document.querySelector('[data-gelangsar-aduan]');

        if (gelangsarAduan) {
            const dekAduan = gelangsarAduan.querySelector('[data-dek-aduan]');
            const kadAduan = [...gelangsarAduan.querySelectorAll('[data-kad-aduan]')];
            const penunjukAduan = [
                ...gelangsarAduan.querySelectorAll('[data-pilih-aduan]'),
            ];

            let indeksSemasa = 0;
            let sedangBergerak = false;
            let mulaX = 0;

            const kemaskiniDekAduan = () => {
                kadAduan.forEach((kad, indeks) => {
                    const jarak = (indeks - indeksSemasa + kadAduan.length)
                        % kadAduan.length;

                    kad.classList.remove(
                        'aktif',
                        'kedua',
                        'ketiga',
                        'tersembunyi',
                        'keluar-kiri',
                        'keluar-kanan'
                    );

                    kad.classList.add(
                        jarak === 0
                            ? 'aktif'
                            : jarak === 1
                                ? 'kedua'
                                : jarak === 2
                                    ? 'ketiga'
                                    : 'tersembunyi'
                    );

                    kad.setAttribute(
                        'aria-hidden',
                        jarak === 0 ? 'false' : 'true'
                    );
                });

                penunjukAduan.forEach((titik, indeks) => {
                    titik.classList.toggle('aktif', indeks === indeksSemasa);
                });
            };

            const tukarAduan = (sasaran, arah = 'seterusnya') => {
                if (
                    sedangBergerak ||
                    sasaran < 0 ||
                    sasaran >= kadAduan.length ||
                    sasaran === indeksSemasa
                ) {
                    return;
                }

                sedangBergerak = true;

                kadAduan[indeksSemasa].classList.add(
                    arah === 'seterusnya' ? 'keluar-kiri' : 'keluar-kanan'
                );

                window.setTimeout(() => {
                    indeksSemasa = sasaran;
                    kemaskiniDekAduan();
                    sedangBergerak = false;
                }, 230);
            };

            gelangsarAduan
                .querySelector('[data-seterusnya-aduan]')
                ?.addEventListener('click', () => {
                    tukarAduan(
                        (indeksSemasa + 1) % kadAduan.length,
                        'seterusnya'
                    );
                });

            gelangsarAduan
                .querySelector('[data-sebelum-aduan]')
                ?.addEventListener('click', () => {
                    tukarAduan(
                        (indeksSemasa - 1 + kadAduan.length) % kadAduan.length,
                        'sebelumnya'
                    );
                });

            penunjukAduan.forEach((titik) => {
                titik.addEventListener('click', () => {
                    const sasaran = Number(titik.dataset.pilihAduan);

                    tukarAduan(
                        sasaran,
                        sasaran > indeksSemasa ? 'seterusnya' : 'sebelumnya'
                    );
                });
            });

            dekAduan?.addEventListener('pointerdown', (event) => {
                mulaX = event.clientX;
            });

            dekAduan?.addEventListener('pointerup', (event) => {
                const jarak = event.clientX - mulaX;

                if (Math.abs(jarak) < 45) {
                    return;
                }

                if (jarak < 0) {
                    tukarAduan(
                        (indeksSemasa + 1) % kadAduan.length,
                        'seterusnya'
                    );

                    return;
                }

                tukarAduan(
                    (indeksSemasa - 1 + kadAduan.length) % kadAduan.length,
                    'sebelumnya'
                );
            });

            kemaskiniDekAduan();
        }

    </script>
@endsection