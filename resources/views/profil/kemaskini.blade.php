@extends('layouts.utama')

@section('tajuk', 'Profil Saya')

@section('kandungan')
    <div class="mb-4">
        <p class="teks-kecil mb-1">AKAUN SAYA</p>
        <h1 class="tajuk-halaman mb-2">Profil Saya</h1>
        <p class="teks-penerangan mb-0">
            Kemaskini maklumat akaun dan kata laluan anda.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="kad-data">
                <div class="p-4 p-md-5">
                    <form method="POST" action="{{ route('profil.update') }}">
                        @csrf
                        @method('PUT')

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
                                    value="{{ old('usr_name', $pengguna->usr_name) }}"
                                    maxlength="100"
                                    required
                                >

                                @error('usr_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Peranan</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $pengguna->usr_level }}"
                                    disabled
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_password" class="form-label fw-semibold">
                                    Kata Laluan Baharu
                                </label>

                                <input
                                    type="password"
                                    class="form-control @error('usr_password') is-invalid @enderror"
                                    id="usr_password"
                                    name="usr_password"
                                    autocomplete="new-password"
                                >

                                <div class="form-text">
                                    Kosongkan jika kata laluan tidak perlu ditukar.
                                </div>

                                @error('usr_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="usr_password_confirmation" class="form-label fw-semibold">
                                    Pengesahan Kata Laluan Baharu
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="usr_password_confirmation"
                                    name="usr_password_confirmation"
                                    autocomplete="new-password"
                                >
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top mt-5 pt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-2"></i>Kemaskini Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection