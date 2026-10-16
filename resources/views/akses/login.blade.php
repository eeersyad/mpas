@extends('layouts.akses')

@section('tajuk', 'Log Masuk')

@section('kandungan')
    <main class="min-vh-100 d-flex align-items-center justify-content-center p-3 position-relative overflow-hidden">
        <div class="w-100" style="max-width: 420px;">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                            <img
                                src="{{ asset('images/logo-mdm.png') }}"
                                alt="Logo Majlis Daerah Marang"
                                class="logo-login"
                            >

                            <img
                                src="{{ asset('images/logo-mpas-2.png') }}"
                                alt="Logo Marang Property Asset System"
                                class="logo-login"
                            >
                        </div>
                    </div>

                    <form method="POST" action="{{ route('authenticate') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="usr_name" class="form-label fw-semibold">
                                Nama Pengguna
                            </label>

                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input
                                    type="text"
                                    class="form-control @error('usr_name') is-invalid @enderror"
                                    id="usr_name"
                                    name="usr_name"
                                    value="{{ old('usr_name') }}"
                                    autocomplete="username"
                                    autofocus
                                    required
                                >
                            </div>

                            @error('usr_name')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                Kata Laluan
                            </label>

                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    autocomplete="current-password"
                                    required
                                >
                            </div>

                            @error('password')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Log Masuk
                        </button>
                    </form>
                    <div class="border-top mt-4 pt-3 text-center">
                        <p class="text-secondary small mb-0">
                            Hakmilik Cipta Majlis Daerah Marang © 2025
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection