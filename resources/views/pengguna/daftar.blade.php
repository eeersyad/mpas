@extends('layouts.utama')

@section('tajuk', 'Daftar Pengguna')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN SISTEM</p>
            <h1 class="tajuk-halaman mb-2">Daftar Pengguna</h1>
            <p class="teks-penerangan mb-0">
                Lengkapkan maklumat untuk mendaftarkan pengguna baharu.
            </p>
        </div>

        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="kad-data">
                <div class="p-4 p-md-5">
                    <form method="POST" action="{{ route('pengguna.store') }}">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <label for="usr_name" class="form-label fw-semibold">
                                    Nama Pengguna <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control @error('usr_name') is-invalid @enderror"
                                    id="usr_name"
                                    name="usr_name"
                                    value="{{ old('usr_name') }}"
                                    maxlength="100"
                                    autocomplete="username"
                                    required
                                >

                                @error('usr_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_level" class="form-label fw-semibold">
                                    Peranan <span class="text-danger">*</span>
                                </label>

                                <select
                                    class="form-select @error('usr_level') is-invalid @enderror"
                                    id="usr_level"
                                    name="usr_level"
                                    required
                                >
                                    <option value="">-- Pilih Peranan --</option>

                                    @foreach ($peranan as $item)
                                        <option value="{{ $item }}" @selected(old('usr_level') === $item)>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('usr_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_password" class="form-label fw-semibold">
                                    Kata Laluan <span class="text-danger">*</span>
                                </label>

                                <div class="input-kata-laluan">
                                    <input type="password" class="form-control @error('usr_password') is-invalid @enderror" id="usr_password" name="usr_password" autocomplete="new-password" required>

                                    <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#usr_password" aria-label="Papar kata laluan" title="Papar kata laluan">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                                <div class="form-text">
                                    Min. 8 aksara, huruf besar, kecil & nombor
                                </div>

                                @error('usr_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_password_confirmation" class="form-label fw-semibold">
                                    Pengesahan Kata Laluan <span class="text-danger">*</span>
                                </label>

                                <div class="input-kata-laluan">
                                    <input type="password" class="form-control" id="usr_password_confirmation" name="usr_password_confirmation" autocomplete="new-password" required>

                                    <button type="button" class="btn butang-lihat-kata-laluan" data-togol-kata-laluan="#usr_password_confirmation" aria-label="Papar kata laluan" title="Papar kata laluan">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_status" class="form-label fw-semibold">
                                    Status <span class="text-danger">*</span>
                                </label>

                                <select
                                    class="form-select @error('usr_status') is-invalid @enderror"
                                    id="usr_status"
                                    name="usr_status"
                                    required
                                >
                                    <option value="ACTIVE" @selected(old('usr_status', 'ACTIVE') === 'ACTIVE')>
                                        Aktif
                                    </option>
                                    <option value="INACTIVE" @selected(old('usr_status') === 'INACTIVE')>
                                        Tidak Aktif
                                    </option>
                                </select>

                                @error('usr_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 border-top mt-5 pt-4">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-light px-4">
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-2"></i>Simpan Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection