@extends('layouts.awam')

@section('tajuk', 'Semak Status Aduan')
@section('kelas_body', 'halaman-aduan-apl')

@section('kandungan')
    <div class="bar-aduan-apl d-md-none">
        <a href="{{ route('portal.sewa') }}" class="butang-kembali-apl" data-kembali aria-label="Kembali"><i class="bi bi-arrow-left"></i></a>
        <button type="button" class="butang-kembali-bar" onclick="history.back()" aria-label="Kembali"><i class="bi bi-arrow-left"></i></button>
        <img src="{{ asset('images/logo-mpas-2.png') }}" alt="MPAS">
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            <header class="awam-hero mb-4">
                <div class="awam-logo mb-3">
                    <img src="{{ asset('images/logo-mpas-2.png') }}" alt="Logo MPAS">
                </div>

                <div class="maklumat-aset-awam">
                    <h1>Semak Status Aduan</h1>
                    <p class="alamat-aset-awam">
                        <i class="bi bi-building"></i>
                        <span>Majlis Daerah Marang · Aduan aset bangunan</span>
                    </p>
                </div>
            </header>

            <section class="kad-awam borang-awam">
                <div class="kepala-borang-awam mb-4">
                    <span class="ikon-borang-awam">
                        <i class="bi bi-search"></i>
                    </span>

                    <div>
                        <p class="teks-kecil mb-1">SEMAKAN ADUAN</p>
                        <h2 class="h5 fw-bold mb-1">Maklumat Aduan</h2>
                        <p class="text-secondary small mb-0">Masukkan no. rujukan dan alamat emel yang digunakan semasa membuat aduan.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('aduan.status.cari') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="rujukan" class="form-label fw-semibold">No. Rujukan Aduan <span class="text-danger">*</span></label>

                        <div class="medan-ikon-awam">
                            <i class="bi bi-hash" aria-hidden="true"></i>
                            <input id="rujukan" type="text" name="rujukan" class="form-control text-uppercase @error('rujukan') is-invalid @enderror" value="{{ old('rujukan') }}" placeholder="Contoh: ADU-2026-00042" maxlength="20" autocomplete="off" autocapitalize="characters" required>
                        </div>

                        @error('rujukan')
                            @if (! old('rujukan'))
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="emel" class="form-label fw-semibold">Alamat Emel <span class="text-danger">*</span></label>

                        <div class="medan-ikon-awam">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <input id="emel" type="email" name="emel" class="form-control @error('emel') is-invalid @enderror" value="{{ old('emel') }}" placeholder="nama@email.com" maxlength="150" autocomplete="email" inputmode="email" required>
                        </div>

                        @error('emel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="bar-hantar-aduan">
                        <button type="submit" class="btn btn-utama w-100 butang-hantar-awam butang-hantar-aduan">
                            <i class="bi bi-search me-2"></i>Semak Status
                        </button>
                    </div>
                </form>
            </section>

            <section class="kad-awam mt-4 nota-status-awam">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <strong>Di mana no. rujukan aduan?</strong>
                    <p class="mb-0">No. rujukan dihantar ke emel anda sejurus selepas aduan dibuat. Emel yang sama juga mengandungi pautan terus ke halaman status aduan.</p>
                </div>
            </section>
        </div>
    </div>
    @if ($errors->has('rujukan') && old('rujukan'))
        <script>
            // Aduan tidak dijumpai: popup kecil gaya aplikasi
            window.addEventListener('load', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Aduan tidak dijumpai',
                    text: @json($errors->first('rujukan')),
                    width: 320,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#168A7E',
                    customClass: { popup: 'popup-kecil-apl' },
                });
            });
        </script>
    @endif

    <script>
        // Butang kembali: ingat halaman asal (cth. pautan aduan atau /awam) semasa mula masuk ke semakan status.
        // Percubaan semakan yang gagal tidak menukar halaman asal.
        (() => {
            const kunci = 'mpasAsalSemakStatus';
            const rujuk = document.referrer;

            try {
                if (rujuk && new URL(rujuk).origin === location.origin && !new URL(rujuk).pathname.startsWith('/status-aduan')) {
                    sessionStorage.setItem(kunci, rujuk);
                }
            } catch (e) {}

            // Hantar semakan tanpa muat semula halaman: semakan gagal tidak menambah sejarah browser
            const borang = document.querySelector('form[action="{{ route('aduan.status.cari') }}"]');
            borang?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const butang = borang.querySelector('[type="submit"]');
                const asalButang = butang.innerHTML;
                butang.disabled = true;
                butang.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyemak...';

                try {
                    const jawapan = await fetch(borang.action, { method: 'POST', body: new FormData(borang), headers: { 'Accept': 'text/html' }, credentials: 'same-origin' });
                    const destinasi = new URL(jawapan.url);

                    if (destinasi.pathname.startsWith('/status-aduan/')) {
                        location.href = jawapan.url;
                        return;
                    }

                    const html = new DOMParser().parseFromString(await jawapan.text(), 'text/html');
                    const ralatRujukan = html.querySelector('#rujukan.is-invalid') ? (html.querySelector('#rujukan')?.closest('.mb-3')?.querySelector('.invalid-feedback')?.textContent.trim()) : null;
                    const ralatEmel = html.querySelector('#emel')?.closest('.mb-4')?.querySelector('.invalid-feedback')?.textContent.trim();
                    const tokenBaru = html.querySelector('input[name="_token"]')?.value;
                    if (tokenBaru) borang.querySelector('input[name="_token"]').value = tokenBaru;

                    Swal.fire({
                        icon: 'error',
                        title: (ralatEmel || ralatRujukan) ? 'Maklumat tidak lengkap' : 'Aduan tidak dijumpai',
                        text: ralatEmel || ralatRujukan || 'Sila semak no. rujukan dan alamat emel.',
                        width: 320,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#168A7E',
                        customClass: { popup: 'popup-kecil-apl' },
                    });
                } catch (e) {
                    borang.submit();
                    return;
                } finally {
                    butang.disabled = false;
                    butang.innerHTML = asalButang;
                }
            });

            document.querySelector('[data-kembali]')?.addEventListener('click', (event) => {
                let asal = null;
                try { asal = sessionStorage.getItem(kunci); } catch (e) {}

                if (asal) {
                    event.preventDefault();
                    location.href = asal;
                }
            });
        })();
    </script>
@endsection
