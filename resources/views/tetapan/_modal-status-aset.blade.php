{{--
    Parameter:
    $idModal    - ID unik modal
    $tajuk      - Tajuk modal
    $action     - URL hantar borang
    $method     - 'POST' atau 'PUT'
    $statusAset - Model StatusAset atau null (status baharu)
    $jenis      - BANGUNAN / TANAH (dari halaman)
--}}
@php
    $gunaOld = old('_modal') === $idModal;
    $jenisModal = $statusAset?->sts_jenis ?? $jenis;

    $nilaiLabel = $gunaOld ? old('sts_label') : ($statusAset?->sts_label ?? '');
    $nilaiWarna = $gunaOld
        ? (old('sts_warna') === 'suai' ? strtoupper((string) old('sts_warna_suai')) : old('sts_warna'))
        : ($statusAset?->sts_warna ?? 'hijau');
    $warnaSuai = \App\Models\StatusAset::adalahHex($nilaiWarna);
    $nilaiIklan = $gunaOld ? (bool) old('sts_boleh_iklan') : (bool) ($statusAset?->sts_boleh_iklan ?? false);
    $nilaiAktif = $gunaOld
        ? in_array(old('sts_status'), ['ACTIVE', '1'], true)
        : ($statusAset?->sts_status ?? 'ACTIVE') === 'ACTIVE';
@endphp

<div class="modal fade modal-lembaran-apl" id="{{ $idModal }}" tabindex="-1" aria-labelledby="{{ $idModal }}Tajuk" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ $action }}" class="modal-content modal-komponen" data-borang-status-aset>
            @csrf

            @if ($method === 'PUT')
                @method('PUT')
            @else
                <input type="hidden" name="sts_jenis" value="{{ $jenisModal }}">
            @endif

            <input type="hidden" name="_modal" value="{{ $idModal }}">

            <div class="modal-header">
                <div>
                    <small class="d-block fw-semibold">STATUS ASET</small>
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
                    <label for="{{ $idModal }}Label" class="form-label fw-semibold small">
                        Nama Status <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="{{ $idModal }}Label" name="sts_label" value="{{ $nilaiLabel }}" class="form-control" maxlength="100" placeholder="Contoh: Kosong Sedia Disewa" required>

                    @if ($statusAset)
                        <div class="form-text">Kod: <code>{{ $statusAset->sts_kod }}</code> (tidak boleh diubah)</div>
                    @else
                        <div class="form-text">Kod dijana secara automatik daripada nama dan tidak boleh diubah kemudian.</div>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="form-label fw-semibold small d-block">Warna Badge <span class="text-danger">*</span></span>

                    <div class="pilihan-warna-status">
                        @foreach (\App\Models\StatusAset::WARNA as $kodWarna => $labelWarna)
                            <input type="radio" class="btn-check" name="sts_warna" id="{{ $idModal }}Warna{{ $kodWarna }}" value="{{ $kodWarna }}" autocomplete="off" @checked($nilaiWarna === $kodWarna)>
                            <label for="{{ $idModal }}Warna{{ $kodWarna }}" class="titik-warna-status status-{{ $kodWarna }}" title="{{ $labelWarna }}"><span class="visually-hidden">{{ $labelWarna }}</span></label>
                        @endforeach

                        {{-- Warna lain: pemilih warna browser --}}
                        <input type="radio" class="btn-check" name="sts_warna" id="{{ $idModal }}WarnaSuai" value="suai" autocomplete="off" data-radio-warna-suai @checked($warnaSuai)>
                        <label for="{{ $idModal }}WarnaSuai" @class(['titik-warna-status', 'titik-warna-suai', 'ada-warna' => $warnaSuai]) style="{{ $warnaSuai ? '--warna-status: ' . $nilaiWarna . ';' : '' }}" title="Warna lain" data-label-warna-suai>
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                            <span class="visually-hidden">Warna lain</span>
                            <input type="color" name="sts_warna_suai" value="{{ $warnaSuai ? $nilaiWarna : '#E91E63' }}" class="input-warna-suai" aria-label="Pilih warna lain" data-input-warna-suai>
                        </label>
                    </div>

                    <div class="mt-2 small text-secondary">Pratonton: <span class="lencana-jadual" data-pratonton-status></span></div>
                </div>

                @if ($jenisModal === 'BANGUNAN')
                    <div class="form-check form-switch mb-1">
                        <input type="hidden" name="sts_boleh_iklan" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="{{ $idModal }}Iklan" name="sts_boleh_iklan" value="1" @checked($nilaiIklan)>
                        <label class="form-check-label fw-semibold small" for="{{ $idModal }}Iklan">Boleh diiklankan</label>
                    </div>
                    <div class="form-text mt-0 mb-3">Jika dihidupkan, bangunan dengan status ini boleh ditanda untuk diiklankan di Portal Awam.</div>
                @endif

                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="sts_status" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="{{ $idModal }}Status" name="sts_status" value="1" @checked($nilaiAktif)>
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
