@extends('layouts.utama')

@section('tajuk', 'Tetapan Akses Penyelenggaraan')

@section('kandungan')
    @php
        $perananLihat = old('peranan_lihat', $tetapan->tpn_peranan_lihat ?? []);
        $perananIsi = old('peranan_isi', $tetapan->tpn_peranan_isi ?? []);
    @endphp

    <div class="hero-tetapan mb-4">
        <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
        <h1 class="tajuk-halaman mb-2">Tetapan</h1>
    </div>

    <div class="row g-4 pusat-tetapan">
        <div class="col-12 col-lg-3">
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

            <form
                method="POST"
                action="{{ route('tetapan.penyelenggaraan.kemaskini') }}"
                data-borang-akses-penyelenggaraan
            >
                @csrf
                @method('PUT')

                {{-- Kepala --}}
                <div class="kad-data kad-tetapan mb-4">
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
                <div class="row g-3 mb-4">
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
                <div class="bar-simpan-tetapan">
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