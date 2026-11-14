@extends('layouts.utama')

@section('tajuk', 'Profil Saya')

@section('kandungan')
    @php
        $inisial = strtoupper(substr($pengguna->usr_name, 0, 1));
    @endphp

    @include('tetapan._bar-apl', ['tajuk' => 'Profil Saya', 'borang' => 'borangProfil', 'kembali' => route('dashboard')])

    <div class="mb-4 d-none d-md-block">
        <p class="teks-kecil mb-1">AKAUN SAYA</p>
        <h1 class="tajuk-halaman mb-2">Profil Saya</h1>
        <p class="teks-penerangan mb-0">
            Urus maklumat akaun dan keselamatan akses anda.
        </p>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-4 d-none d-md-block">
            <aside class="kad-profil-identiti">
                <div class="hiasan-profil"></div>

                <div class="avatar-profil-besar">
                    {{ $inisial }}
                </div>

                <div class="text-center">
                    <h2>{{ $pengguna->usr_name }}</h2>

                    <span class="lencana-peranan-profil">
                        <i class="bi bi-person-badge-fill"></i>
                        {{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}
                    </span>
                </div>

                <div class="status-akaun-profil">
                    <span class="titik-status-aktif"></span>
                    Akaun aktif
                </div>

                <div class="maklumat-profil-ringkas">
                    <div class="item-maklumat-profil">
                        <span class="ikon-maklumat-profil">
                            <i class="bi bi-person"></i>
                        </span>

                        <div>
                            <small>Nama pengguna</small>
                            <strong>{{ $pengguna->usr_name }}</strong>
                        </div>
                    </div>

                    <div class="item-maklumat-profil">
                        <span class="ikon-maklumat-profil">
                            <i class="bi bi-shield-lock"></i>
                        </span>

                        <div>
                            <small>Tahap akses</small>
                            <strong>{{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}</strong>
                        </div>
                    </div>
                </div>

                <div class="nota-keselamatan-profil">
                    <i class="bi bi-info-circle"></i>
                    <span>Gunakan kata laluan yang sukar diteka untuk melindungi akaun anda.</span>
                </div>
            </aside>
        </div>

        <div class="col-12 col-xl-8">
            <section class="kad-profil-borang borang-pengguna-apl">
                <div class="kepala-profil-borang d-none d-md-flex">
                    <span class="ikon-kepala-profil">
                        <i class="bi bi-pencil-square"></i>
                    </span>

                    <div>
                        <p class="teks-kecil mb-1">KEMAS KINI AKAUN</p>
                        <h2>Maklumat Profil</h2>
                        <p>Kemaskini nama pengguna atau kata laluan jika diperlukan.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profil.update') }}" id="borangProfil" data-pantau-ubah="telefon" data-borang-apl>
                    @csrf
                    @method('PUT')

                    {{-- ===================== TELEFON: profil gaya Settings ===================== --}}
                    <div class="d-md-none tetapan-apl">
                        <div class="kepala-profil-apl">
                            <span class="avatar-apl besar">{{ strtoupper(substr($pengguna->usr_name, 0, 2)) }}</span>

                            <div class="min-w-0">
                                <strong class="d-block text-truncate">{{ $pengguna->usr_name }}</strong>
                                <span class="lencana-peranan-apl">{{ $pengguna->usr_level }} · {{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}</span>
                                <small class="status-profil-apl"><span class="titik-status-aktif"></span>Akaun aktif</small>
                            </div>
                        </div>

                        <p class="label-kumpulan-apl">Akaun</p>

                        <div class="kad-senarai-apl">
                            <button type="button" @class(['baris-tetapan-medan', 'ada-ralat' => $errors->has('usr_name')]) data-sunting-medan="usr_name" data-label="Nama Pengguna">
                                <span class="label-medan-apl">Nama pengguna</span>
                                <strong data-nilai-medan="usr_name" data-kosong="Belum diisi">-</strong>
                                <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                            </button>

                            <div class="baris-tetapan-medan dikunci">
                                <span class="label-medan-apl">Peranan</span>
                                <strong>{{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}</strong>
                                <i class="bi bi-lock-fill anak-panah-apl" aria-label="Tidak boleh diubah"></i>
                            </div>
                        </div>

                        @error('usr_name')
                            <p class="ralat-kumpulan-apl">{{ $message }}</p>
                        @enderror

                        <p class="nota-kumpulan-apl">Peranan hanya boleh diubah oleh pentadbir sistem.</p>

                        <p class="label-kumpulan-apl">Keselamatan</p>

                        <div class="kad-senarai-apl">
                            <button type="button" @class(['baris-tetapan-medan', 'ada-ralat' => $errors->hasAny(['usr_password', 'kata_laluan_semasa'])]) data-bs-toggle="offcanvas" data-bs-target="#lembaranKataLaluan" aria-controls="lembaranKataLaluan">
                                <span class="label-medan-apl">Tukar kata laluan</span>
                                <strong data-status-kata-laluan data-kosong="Tidak ditukar" data-ada="Akan ditukar ✓">-</strong>
                                <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                            </button>
                        </div>

                        @foreach (['kata_laluan_semasa', 'usr_password'] as $medanRalat)
                            @error($medanRalat)
                                <p class="ralat-kumpulan-apl">{{ $message }}</p>
                            @enderror
                        @endforeach

                        <p class="nota-kumpulan-apl">Gunakan kata laluan yang sukar diteka untuk melindungi akaun anda.</p>

                        <div class="kad-senarai-apl mt-4">
                            <button type="submit" form="borangLogKeluarProfil" class="baris-tetapan-medan baris-log-keluar-apl">
                                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                <span class="label-medan-apl">Log keluar</span>
                            </button>
                        </div>
                    </div>

                    <div class="seksyen-profil d-none d-md-block">
                        <div class="tajuk-seksyen-profil">
                            <i class="bi bi-person-vcard"></i>
                            <span>Maklumat Akaun</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="usr_name" class="form-label">
                                    Nama Pengguna <span class="text-danger">*</span>
                                </label>

                                <div class="input-group-profil">
                                    <span><i class="bi bi-person"></i></span>

                                    <input
                                        type="text"
                                        class="form-control @error('usr_name') is-invalid @enderror"
                                        id="usr_name"
                                        name="usr_name"
                                        value="{{ old('usr_name', $pengguna->usr_name) }}"
                                        maxlength="100"
                                        required
                                    >
                                </div>

                                @error('usr_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Peranan Sistem</label>

                                <div class="medan-baca-profil">
                                    <span><i class="bi bi-person-badge"></i></span>
                                    <strong>
                                        {{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}
                                    </strong>
                                    <small>Tidak boleh diubah melalui profil.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="seksyen-profil mb-0 d-none d-md-block">
                        <div class="tajuk-seksyen-profil">
                            <i class="bi bi-key"></i>
                            <span>Keselamatan Akaun</span>
                        </div>

                        <p class="penerangan-seksyen-profil">
                            Kosongkan ruangan di bawah jika anda tidak mahu menukar kata laluan.
                        </p>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="kata_laluan_semasa" class="form-label">
                                    Kata Laluan Semasa
                                </label>

                                <div class="input-group-profil input-kata-laluan">
                                    <span><i class="bi bi-key"></i></span>

                                    <input
                                        type="password"
                                        class="form-control @error('kata_laluan_semasa') is-invalid @enderror"
                                        id="kata_laluan_semasa"
                                        name="kata_laluan_semasa"
                                        autocomplete="current-password"
                                        placeholder="Wajib diisi jika menukar kata laluan"
                                    >

                                    <button
                                        type="button"
                                        class="btn butang-lihat-kata-laluan"
                                        data-togol-kata-laluan="#kata_laluan_semasa"
                                        aria-label="Papar kata laluan"
                                        title="Papar kata laluan"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                                @error('kata_laluan_semasa')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_password" class="form-label">
                                    Kata Laluan Baharu
                                </label>

                                <div class="input-group-profil input-kata-laluan">
                                    <span><i class="bi bi-lock"></i></span>

                                    <input
                                        type="password"
                                        class="form-control @error('usr_password') is-invalid @enderror"
                                        id="usr_password"
                                        name="usr_password"
                                        autocomplete="new-password"
                                        placeholder="Min. 8 aksara, huruf besar, kecil & nombor"
                                    >

                                    <button
                                        type="button"
                                        class="btn butang-lihat-kata-laluan"
                                        data-togol-kata-laluan="#usr_password"
                                        aria-label="Papar kata laluan"
                                        title="Papar kata laluan"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                                @error('usr_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_password_confirmation" class="form-label">
                                    Pengesahan Kata Laluan
                                </label>

                                <div class="input-group-profil input-kata-laluan">
                                    <span><i class="bi bi-shield-check"></i></span>

                                    <input
                                        type="password"
                                        class="form-control"
                                        id="usr_password_confirmation"
                                        name="usr_password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Ulang kata laluan baharu"
                                    >

                                    <button
                                        type="button"
                                        class="btn butang-lihat-kata-laluan"
                                        data-togol-kata-laluan="#usr_password_confirmation"
                                        aria-label="Papar kata laluan"
                                        title="Papar kata laluan"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tindakan-profil d-none d-md-flex">
                        <p>
                            <i class="bi bi-shield-check"></i>
                            Perubahan akan dikemaskini serta-merta.
                        </p>

                        <button type="submit" class="btn btn-utama butang-simpan-profil">
                            <i class="bi bi-check2-circle me-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="borangLogKeluarProfil" class="d-none" data-sahkan-keluar>
        @csrf
    </form>

    @include('tetapan._lembaran-sunting')
    @include('pengguna._lembaran-apl', ['wajib' => false, 'semasa' => true])
@endsection
