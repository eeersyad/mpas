@extends('layouts.utama')

@section('tajuk', 'Tetapan Akses Penyelenggaraan')

@section('kandungan')
    @php
        $perananLihat = old('peranan_lihat', $tetapan->tpn_peranan_lihat ?? []);
        $perananIsi = old('peranan_isi', $tetapan->tpn_peranan_isi ?? []);
    @endphp

    <div class="hero-tetapan mb-4 d-none d-md-block">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
        <p class="teks-penerangan mb-0 d-none d-md-block">Urus konfigurasi dan akses sistem.</p>
    </div>

    @include('tetapan._bar-apl', ['tajuk' => 'Akses Penyelenggaraan', 'borang' => 'borangAksesPenyelenggaraan'])

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3 d-none d-md-block">
            @include('tetapan.menu')
        </div>

        <div class="col-12 col-lg-9">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $ralat)
                            <li>{{ $ralat }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tetapan.penyelenggaraan.kemaskini') }}" id="borangAksesPenyelenggaraan" data-borang-akses-penyelenggaraan data-pantau-ubah>
                @csrf
                @method('PUT')

                {{-- Kepala --}}
                {{-- ===================== TELEFON: matriks akses ===================== --}}
                <div class="d-md-none tetapan-apl">
                    <p class="nota-kumpulan-apl mt-0 mb-2">Peranan yang boleh <strong>isi</strong> akan boleh <strong>lihat</strong> secara automatik.</p>

                    <div class="kad-senarai-apl matriks-akses-apl">
                        <div class="kepala-matriks-apl">
                            <span>Peranan</span>
                            <span>Lihat</span>
                            <span>Isi</span>
                        </div>

                        <div class="baris-matriks-apl berkunci">
                            <span class="avatar-apl">SYS</span>
                            <span class="teks-baris-laporan">
                                <strong>SYSTEM <i class="bi bi-lock-fill text-secondary small"></i></strong>
                                <small>Super Admin</small>
                            </span>
                            <span class="form-check form-switch sel-suis-apl"><input class="form-check-input" type="checkbox" role="switch" checked disabled aria-label="SYSTEM boleh lihat"></span>
                            <span class="form-check form-switch sel-suis-apl"><input class="form-check-input" type="checkbox" role="switch" checked disabled aria-label="SYSTEM boleh isi"></span>
                        </div>

                        @foreach ($peranan as $kod)
                            <div class="baris-matriks-apl">
                                <span class="avatar-apl">{{ $kod }}</span>
                                <span class="teks-baris-laporan">
                                    <strong>{{ $kod }}</strong>
                                    <small>{{ $namaPeranan[$kod] ?? '' }}</small>
                                </span>
                                <span class="form-check form-switch sel-suis-apl"><input class="form-check-input" type="checkbox" role="switch" data-proksi-suis="lihat{{ $kod }}" aria-label="{{ $kod }} boleh lihat"></span>
                                <span class="form-check form-switch sel-suis-apl"><input class="form-check-input" type="checkbox" role="switch" data-proksi-suis="isi{{ $kod }}" aria-label="{{ $kod }} boleh isi"></span>
                            </div>
                        @endforeach
                    </div>

                    <p class="nota-kumpulan-apl">Rekod pemeriksaan tidak boleh dihapus oleh mana-mana peranan.</p>
                </div>

                <div class="kad-data kad-tetapan mb-4 d-none d-md-block">
                    <div class="kepala-kad-tetapan">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <div class="ikon-tetapan bangunan">
                                <i class="bi bi-shield-lock"></i>
                            </div>

                            <div class="min-w-0">
                                <h2 class="h6 fw-bold mb-1">Akses Penyelenggaraan</h2>
                                <p class="mb-0 text-secondary small">
                                    Tetapkan peranan yang boleh melihat dan mengisi rekod
                                    pemeriksaan penyelenggaraan aset bangunan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kad peranan --}}
                <div class="row g-3 mb-4 d-none d-md-flex">
                    {{-- SYSTEM: sentiasa ada akses --}}
                    <div class="col-12 col-md-6">
                        <div class="kad-peranan ada-akses berkunci">
                            <div class="kepala-peranan">
                                <span class="avatar-peranan">SYS</span>

                                <div class="flex-grow-1 min-w-0">
                                    <p class="kod-peranan">
                                        SYSTEM <i class="bi bi-lock-fill text-secondary small ms-1"></i>
                                    </p>
                                    <p class="nama-peranan">Super Admin</p>
                                </div>

                                <span class="lencana-akses penuh">Lihat & Isi</span>
                            </div>

                            <div class="baris-akses">
                                <div>
                                    <p class="label-akses">Boleh Lihat</p>
                                    <p class="penerangan-akses">Sentiasa diberi akses</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input dikunci" type="checkbox" role="switch" checked disabled aria-label="SYSTEM boleh lihat">
                                </div>
                            </div>

                            <div class="baris-akses">
                                <div>
                                    <p class="label-akses">Boleh Isi / Kemas Kini</p>
                                    <p class="penerangan-akses">Sentiasa diberi akses</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input dikunci" type="checkbox" role="switch" checked disabled aria-label="SYSTEM boleh isi">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Peranan lain --}}
                    @foreach ($peranan as $kod)
                        @php
                            $isi = in_array($kod, $perananIsi, true);
                            $lihat = $isi || in_array($kod, $perananLihat, true);
                        @endphp

                        <div class="col-12 col-md-6">
                            <div
                                @class(['kad-peranan', 'ada-akses' => $lihat])
                                data-kad-peranan="{{ $kod }}"
                            >
                                <div class="kepala-peranan">
                                    <span class="avatar-peranan">{{ $kod }}</span>

                                    <div class="flex-grow-1 min-w-0">
                                        <p class="kod-peranan">{{ $kod }}</p>
                                        <p class="nama-peranan text-truncate">{{ $namaPeranan[$kod] ?? '' }}</p>
                                    </div>

                                    <span
                                        @class([
                                            'lencana-akses',
                                            'penuh' => $isi,
                                            'lihat' => ! $isi && $lihat,
                                            'tiada' => ! $lihat,
                                        ])
                                        data-lencana-akses
                                    >
                                        {{ $isi ? 'Lihat & Isi' : ($lihat ? 'Lihat sahaja' : 'Tiada akses') }}
                                    </span>
                                </div>

                                <label class="baris-akses" for="lihat{{ $kod }}">
                                    <div>
                                        <p class="label-akses">Boleh Lihat</p>
                                        <p class="penerangan-akses">Lihat rekod pemeriksaan dalam butiran bangunan</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="lihat{{ $kod }}"
                                            name="peranan_lihat[]"
                                            value="{{ $kod }}"
                                            data-akses-lihat
                                            @checked($lihat)
                                        >
                                    </div>
                                </label>

                                <label class="baris-akses" for="isi{{ $kod }}">
                                    <div>
                                        <p class="label-akses">Boleh Isi / Kemas Kini</p>
                                        <p class="penerangan-akses">Tambah dan kemas kini rekod pemeriksaan</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="isi{{ $kod }}"
                                            name="peranan_isi[]"
                                            value="{{ $kod }}"
                                            data-akses-isi
                                            @checked($isi)
                                        >
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Bar simpan --}}
                <div class="bar-simpan-tetapan d-none d-md-flex">
                    <p class="small text-secondary mb-0">
                        <i class="bi bi-info-circle me-1 text-success"></i>
                        Peranan yang boleh <strong>isi</strong> akan boleh <strong>lihat</strong> secara automatik.
                        Rekod pemeriksaan tidak boleh dihapus oleh mana-mana peranan.
                    </p>

                    <button type="submit" class="btn btn-utama text-nowrap">
                        <i class="bi bi-check-lg me-1"></i>Simpan Tetapan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('skrip')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const borang = document.querySelector('[data-borang-akses-penyelenggaraan]');

            if (! borang) {
                return;
            }

            const kemaskiniKad = (kad) => {
                const lihat = kad.querySelector('[data-akses-lihat]');
                const isi = kad.querySelector('[data-akses-isi]');
                const lencana = kad.querySelector('[data-lencana-akses]');

                // Isi → lihat ditanda dan dikunci (guna class, bukan disabled, supaya nilai dihantar)
                if (isi.checked) {
                    lihat.checked = true;
                }

                lihat.classList.toggle('dikunci', isi.checked);

                // Lencana status
                lencana.classList.remove('penuh', 'lihat', 'tiada');

                if (isi.checked) {
                    lencana.classList.add('penuh');
                    lencana.textContent = 'Lihat & Isi';
                } else if (lihat.checked) {
                    lencana.classList.add('lihat');
                    lencana.textContent = 'Lihat sahaja';
                } else {
                    lencana.classList.add('tiada');
                    lencana.textContent = 'Tiada akses';
                }

                kad.classList.toggle('ada-akses', lihat.checked);
            };

            borang.querySelectorAll('[data-kad-peranan]').forEach((kad) => {
                kemaskiniKad(kad);

                kad.querySelectorAll('[data-akses-lihat], [data-akses-isi]').forEach((suis) => {
                    suis.addEventListener('change', () => kemaskiniKad(kad));
                });
            });
        });
    </script>
@endpush