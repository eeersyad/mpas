@extends('layouts.awam')

@section('tajuk', 'Aduan Aset Bangunan')

@section('kandungan')
    @php
        $bangunan = $kodBangunan->bangunan;

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

            <span class="rujukan-aset-awam">
                <i class="bi bi-upc-scan"></i>
                {{ $bangunan->asb_ref_no }}
            </span>
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

            <div class="mb-4">
                <label for="gambar" class="form-label fw-semibold">
                    Gambar Kerosakan
                </label>

                <input
                    id="gambar"
                    type="file"
                    name="gambar[]"
                    class="form-control @error('gambar') is-invalid @enderror"
                    accept="image/jpeg,image/png,image/webp"
                    capture="environment"
                    multiple
                >

                <div class="form-text">
                    Maksimum empat gambar. Saiz setiap gambar tidak melebihi 5 MB.
                </div>

                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @error('gambar.*')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-utama butang-hantar-aduan w-100 py-3">
                <i class="bi bi-send-fill me-2"></i>
                Hantar Aduan
            </button>
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

        const kemaskiniPenyewa = () => {
            const penyewa = kategoriPengadu.value === 'PENYEWA';

            ruangPenyewa.hidden = !penyewa;
            pilihanPenyewa.required = penyewa;

            if (!penyewa) {
                pilihanPenyewa.value = '';
            }
        };

        kategoriPengadu.addEventListener('change', kemaskiniPenyewa);
        kemaskiniPenyewa();

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