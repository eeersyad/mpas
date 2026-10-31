@extends('layouts.utama')

@section('tajuk', 'Kemaskini Pengguna')

@section('kandungan')
    @php
        $namaPeranan = [
            'SYSTEM' => 'Super Admin',
            'BPH' => 'Bahagian Pengurusan Harta',
            'BP' => 'Bahagian Perancang',
            'KJ' => 'Jabatan Kejuruteraan',
        ];

        $inisial = strtoupper(substr($pengguna->usr_name, 0, 1));
        $aktif = $pengguna->usr_status === 'ACTIVE';
    @endphp

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
            <h1 class="tajuk-halaman mb-2">Kemaskini Pengguna</h1>
            <p class="teks-penerangan mb-0">
                Urus maklumat akaun dan tahap akses pengguna sistem.
            </p>
        </div>

        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-4">
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
                    <span class="{{ $aktif ? 'titik-status-aktif' : 'titik-status-tidak-aktif' }}"></span>
                    {{ $aktif ? 'Akaun aktif' : 'Akaun tidak aktif' }}
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
                            <small>Peranan semasa</small>
                            <strong>
                                {{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="nota-keselamatan-profil">
                    <i class="bi bi-info-circle"></i>
                    <span>
                        Peranan dan status akaun menentukan akses pengguna dalam sistem.
                    </span>
                </div>
            </aside>
        </div>

        <div class="col-12 col-xl-8">
            <section class="kad-profil-borang">
                <div class="kepala-profil-borang">
                    <span class="ikon-kepala-profil">
                        <i class="bi bi-person-gear"></i>
                    </span>

                    <div>
                        <p class="teks-kecil mb-1">KEMAS KINI AKAUN</p>
                        <h2>Maklumat Pengguna</h2>
                        <p>Semak maklumat akaun, peranan, status dan keselamatan pengguna.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
                    @csrf
                    @method('PUT')

                    <div class="seksyen-profil">
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

                            <div class="col-12 col-md-6">
                                <label for="usr_level" class="form-label">
                                    Peranan <span class="text-danger">*</span>
                                </label>

                                <div class="input-group-profil">
                                    <span><i class="bi bi-person-badge"></i></span>

                                    <select
                                        class="form-select @error('usr_level') is-invalid @enderror"
                                        id="usr_level"
                                        name="usr_level"
                                        required
                                    >
                                        @foreach ($peranan as $item)
                                            <option
                                                value="{{ $item }}"
                                                @selected(old('usr_level', $pengguna->usr_level) === $item)
                                            >
                                                {{ $namaPeranan[$item] ?? $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @error('usr_level')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                $statusSemasa = old('usr_status', $pengguna->usr_status);
                                $akaunAktif = $statusSemasa === 'ACTIVE';
                            @endphp

                            <div class="col-12 col-md-6">
                                <label class="form-label">
                                    Status Akaun <span class="text-danger">*</span>
                                </label>

                                <div
                                    class="suis-status-akaun {{ $akaunAktif ? 'aktif' : 'tidak-aktif' }}"
                                    data-suis-status-wrapper
                                >
                                    <input
                                        type="hidden"
                                        name="usr_status"
                                        value="{{ $statusSemasa }}"
                                        data-nilai-status
                                    >

                                    <input
                                        type="checkbox"
                                        id="suisStatusAkaun"
                                        class="visually-hidden"
                                        @checked($akaunAktif)
                                        data-suis-status
                                    >

                                    <label for="suisStatusAkaun" class="kawalan-suis-status">
                                        <span class="landasan-suis-status">
                                            <span class="butang-suis-status"></span>
                                        </span>

                                        <span class="maklumat-suis-status">
                                            <strong data-label-status>
                                                {{ $akaunAktif ? 'Akaun Aktif' : 'Akaun Tidak Aktif' }}
                                            </strong>

                                            <small data-penerangan-status>
                                                {{ $akaunAktif
                                                    ? 'Pengguna boleh mengakses sistem.'
                                                    : 'Akses pengguna disekat buat sementara waktu.' }}
                                            </small>
                                        </span>
                                    </label>
                                </div>

                                @error('usr_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="seksyen-profil mb-0">
                        <div class="tajuk-seksyen-profil">
                            <i class="bi bi-key"></i>
                            <span>Keselamatan Akaun</span>
                        </div>

                        <p class="penerangan-seksyen-profil">
                            Kosongkan kedua-dua medan jika kata laluan pengguna tidak perlu ditukar.
                        </p>

                        <div class="row g-3">
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
                                        placeholder="Minimum 8 aksara"
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

                    <div class="tindakan-profil">
                        <p>
                            <i class="bi bi-shield-check"></i>
                            Perubahan akan dikemaskini serta-merta.
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-light px-4">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-utama butang-simpan-profil">
                                <i class="bi bi-check2-circle me-2"></i>
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection