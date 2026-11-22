{{--
    Parameter:
    $idModal - ID unik modal
    $tajuk   - Tajuk modal
    $action  - URL hantar borang
    $method  - 'POST' atau 'PUT'
    $akses   - Model AksesPenyelenggaraan atau null (pengguna baharu)
--}}
@php
    $gunaOld = old('_modal') === $idModal;

    $nilaiPengguna = $gunaOld ? old('usr_id') : ($akses?->usr_id ?? '');
    $nilaiTahap = $gunaOld ? old('tap_tahap') : ($akses?->tap_tahap ?? 'LIHAT');
    $nilaiAktif = $gunaOld
        ? in_array(old('tap_status'), ['ACTIVE', '1'], true)
        : ($akses?->tap_status ?? 'ACTIVE') === 'ACTIVE';
@endphp

<div class="modal fade modal-lembaran-apl" id="{{ $idModal }}" tabindex="-1" aria-labelledby="{{ $idModal }}Tajuk" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ $action }}" class="modal-content modal-komponen">
            @csrf

            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <input type="hidden" name="_modal" value="{{ $idModal }}">

            <div class="modal-header">
                <div>
                    <small class="d-block fw-semibold">AKSES PENYELENGGARAAN</small>
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
                    <label for="{{ $idModal }}Pengguna" class="form-label fw-semibold small">
                        Pengguna <span class="text-danger">*</span>
                    </label>

                    @if ($akses)
                        <input type="text" id="{{ $idModal }}Pengguna" class="form-control" value="{{ $akses->pengguna->usr_name }} ({{ $akses->pengguna->usr_level }})" disabled>
                    @elseif ($penggunaBoleh->isEmpty())
                        <input type="text" id="{{ $idModal }}Pengguna" class="form-control" value="Semua pengguna aktif sudah ada dalam senarai" disabled>
                    @else
                        <select id="{{ $idModal }}Pengguna" name="usr_id" class="form-select" required>
                            <option value="">-- Pilih Pengguna --</option>

                            @foreach ($penggunaBoleh as $pengguna)
                                <option value="{{ $pengguna->usr_id }}" @selected((string) $nilaiPengguna === (string) $pengguna->usr_id)>
                                    {{ $pengguna->usr_name }} ({{ $pengguna->usr_level }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="{{ $idModal }}Tahap" class="form-label fw-semibold small">
                        Tahap Akses <span class="text-danger">*</span>
                    </label>
                    <select id="{{ $idModal }}Tahap" name="tap_tahap" class="form-select" data-segmen required>
                        @foreach (\App\Models\AksesPenyelenggaraan::SENARAI_TAHAP as $kod => $label)
                            <option value="{{ $kod }}" @selected($nilaiTahap === $kod)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Lihat sahaja: lihat rekod pemeriksaan. Lihat &amp; Isi: boleh tambah dan kemas kini rekod.</div>
                </div>

                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="tap_status" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="{{ $idModal }}Status" name="tap_status" value="1" @checked($nilaiAktif)>
                    <label class="form-check-label fw-semibold small" for="{{ $idModal }}Status">Aktif</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-utama" @disabled(! $akses && $penggunaBoleh->isEmpty())>
                    <i class="bi bi-check2 me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
