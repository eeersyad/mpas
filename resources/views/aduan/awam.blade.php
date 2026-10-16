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

    <div class="awam-jenama text-center mb-4">
        <div class="awam-ikon mb-3">
            <i class="bi bi-buildings-fill"></i>
        </div>

        <p class="teks-kecil mb-1">MARANG PROPERTY ASSET SYSTEM</p>
        <h1 class="h3 fw-bold mb-2">Aduan Aset Bangunan</h1>
        <p class="text-secondary mb-0">
            {{ $bangunan->asb_name }}
        </p>
        <small class="text-secondary">{{ $bangunan->asb_ref_no }}</small>
    </div>

    @if ($progressAduan->isNotEmpty())
        <section class="kad-awam mb-4">
            <h2 class="h6 fw-bold mb-3">
                <i class="bi bi-activity me-2 text-success"></i>
                Status Aduan Semasa
            </h2>

            <div class="vstack gap-3">
                @foreach ($progressAduan as $item)
                    @php
                        $maklumatStatus = $status[$item->cmp_status];
                    @endphp

                    <div class="progress-aduan">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <div>
                                <strong class="d-block">
                                    {{ $item->cmp_damage_type }}
                                </strong>

                                <small class="text-secondary">
                                    {{ $item->cmp_received_date?->format('d/m/Y') }}
                                </small>
                            </div>

                            <span class="badge text-bg-{{ $maklumatStatus['warna'] }} align-self-start">
                                {{ $maklumatStatus['label'] }}
                            </span>
                        </div>

                        <div class="progress" role="progressbar">
                            <div
                                class="progress-bar bg-{{ $maklumatStatus['warna'] }}"
                                style="width: {{ $maklumatStatus['peratus'] }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-secondary small mt-3 mb-0">
                Aduan selesai dipaparkan selama {{ $tempohHari }} hari.
            </p>
        </section>
    @endif

    <section class="kad-awam">
        <div class="mb-4">
            <h2 class="h5 fw-bold mb-1">Buat Aduan Baharu</h2>
            <p class="text-secondary small mb-0">
                Sila isi maklumat di bawah untuk melaporkan kerosakan.
            </p>
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

            <button type="submit" class="btn btn-utama w-100 py-3">
                <i class="bi bi-send-fill me-2"></i>
                Hantar Aduan
            </button>
        </form>
    </section>

    <p class="text-center text-secondary small mt-4 mb-0">
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

    </script>
@endsection