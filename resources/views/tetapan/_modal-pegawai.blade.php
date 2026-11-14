{{--
    Parameter:
    $idModal - ID unik modal
    $tajuk   - Tajuk modal
    $action  - URL hantar borang
    $method  - 'POST' atau 'PUT'
    $pegawai - Model PegawaiAset atau null (pegawai baharu)
--}}
@php
    $gunaOld = old('_modal') === $idModal;

    $nilaiNama = $gunaOld ? old('pgw_nama') : ($pegawai?->pgw_nama ?? '');
    $nilaiJawatan = $gunaOld ? old('pgw_jawatan') : ($pegawai?->pgw_jawatan ?? '');
    $nilaiPeranan = $gunaOld ? old('pgw_peranan') : ($pegawai?->pgw_peranan ?? '');
    $nilaiAktif = $gunaOld
        ? in_array(old('pgw_status'), ['ACTIVE', '1'], true)
        : ($pegawai?->pgw_status ?? 'ACTIVE') === 'ACTIVE';
@endphp

<div class="modal fade" id="{{ $idModal }}" tabindex="-1" aria-labelledby="{{ $idModal }}Tajuk" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ $action }}" class="modal-content modal-komponen">
            @csrf

            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <input type="hidden" name="_modal" value="{{ $idModal }}">

            <div class="modal-header">
                <div>
                    <small class="d-block fw-semibold">PEGAWAI ASET</small>
                    <h5 class="modal-title fw-bold mb-0" id="{{ $idModal }}Tajuk">{{ $tajuk }}</h5>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                @if ($gunaOld && $errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $ralat)
                                <li>{{ $ralat }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="{{ $idModal }}Nama" class="form-label fw-semibold small">
                        Nama Pegawai <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="{{ $idModal }}Nama" name="pgw_nama" value="{{ $nilaiNama }}" class="form-control" maxlength="255" placeholder="Contoh: Ahmad Bin Ali" required>
                </div>

                <div class="mb-3">
                    <label for="{{ $idModal }}Jawatan" class="form-label fw-semibold small">
                        Jawatan <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="{{ $idModal }}Jawatan" name="pgw_jawatan" value="{{ $nilaiJawatan }}" class="form-control" maxlength="255" placeholder="Contoh: Penolong Pegawai Penilaian" required>
                </div>

                <div class="mb-3">
                    <label for="{{ $idModal }}Peranan" class="form-label fw-semibold small">
                        Peranan <span class="text-danger">*</span>
                    </label>
                    <select id="{{ $idModal }}Peranan" name="pgw_peranan" class="form-select" required>
                        <option value="">-- Pilih Peranan --</option>

                        @foreach (\App\Models\PegawaiAset::SENARAI_PERANAN as $peranan)
                            <option value="{{ $peranan }}" @selected($nilaiPeranan === $peranan)>
                                {{ \App\Support\Paparan::label($peranan) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Peranan menentukan dropdown mana pegawai ini dipaparkan dalam borang bangunan.</div>
                </div>

                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="pgw_status" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="{{ $idModal }}Status" name="pgw_status" value="1" @checked($nilaiAktif)>
                    <label class="form-check-label fw-semibold small" for="{{ $idModal }}Status">Aktif</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-utama">
                    <i class="bi bi-check2 me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
