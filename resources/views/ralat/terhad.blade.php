@extends('layouts.akses')

@section('tajuk', 'Akses Terhad')

@section('kandungan')
    <main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
        <div class="card border-0 shadow-sm rounded-4" style="max-width: 460px;">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger mb-3"
                    style="width: 64px; height: 64px;">
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                </div>

                <h1 class="h4 fw-bold">Akses Terhad</h1>

                <p class="text-secondary mb-4">
                    Anda tidak mempunyai kebenaran untuk membuka halaman ini.
                </p>

                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </main>
@endsection