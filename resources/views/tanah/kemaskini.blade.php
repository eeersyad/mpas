@extends('layouts.utama')

@section('tajuk', 'Kemaskini Aset Tanah')

@section('kandungan')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <p class="teks-kecil mb-1">PENGURUSAN ASET</p>
            <h1 class="tajuk-halaman mb-2">Kemaskini Aset Tanah</h1>
            <p class="teks-penerangan mb-0">
                No. rujukan: <strong>{{ $tanah->asl_ref_no }}</strong>
            </p>
        </div>

        <a href="{{ route('tanah.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @include('tanah.borang', [
        'tanah' => $tanah,
        'action' => route('tanah.update', $tanah),
        'method' => 'PUT',
        'butang' => 'Kemaskini Aset Tanah',
    ])
@endsection