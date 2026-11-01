<div class="modal-header">
    <div>
        <p class="teks-kecil mb-1">BUTIRAN ASET BANGUNAN</p>
        <h5 class="modal-title fw-bold">{{ $bangunan->asb_name }}</h5>
        <small class="text-secondary">{{ $bangunan->asb_ref_no }}</small>
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="modal"
        aria-label="Tutup"
    ></button>
</div>

<div class="modal-body p-4">
    <div class="tab-butiran-melekat">
        <div class="tab-butiran" role="tablist" aria-label="Submenu butiran aset bangunan">
            @foreach ([
                1 => 'Maklumat Asas',
                2 => 'Lokasi & Agensi',
                3 => 'Maklumat Fizikal',
                4 => 'Kos & Penilaian',
                5 => 'Status & Penyelenggaraan',
                6 => 'Pengumpulan & Pengesahan',
                7 => 'Penyewa',
                8 => 'Dokumen',
            ] as $nombor => $label)
                <button
                    type="button"
                    class="tab-butiran-butang"
                    data-tab-butiran-butang="{{ $nombor }}"
                    data-label="{{ $label }}"
                    role="tab"
                    aria-selected="false"
                    aria-label="{{ $label }}"
                    title="{{ $label }}"
                >
                    <span>{{ $nombor }}</span>
                    <em class="label-tab">{{ $label }}</em>
                </button>
            @endforeach
        </div>

        <p class="label-tab-aktif" data-label-tab-aktif>{{ 'Maklumat Asas' }}</p>
    </div>

    <section class="panel-butiran aktif" data-tab-butiran-panel="1" role="tabpanel">
        <div class="butiran-seksyen">
            <h6>Maklumat Asas</h6>
            <p>Butiran asas, kategori dan alamat aset bangunan.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>No. Rujukan</dt>
                    <dd>{{ $bangunan->asb_ref_no ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Kategori</dt>
                    <dd>{{ $bangunan->asb_category ? \App\Support\Paparan::label($bangunan->asb_category) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Nama Bangunan</dt>
                    <dd>{{ $bangunan->asb_name ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>No. Fail</dt>
                    <dd>{{ $bangunan->asb_no_file ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Subkategori</dt>
                    <dd>{{ $bangunan->asb_sub_category ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Poskod</dt>
                    <dd>{{ $bangunan->asb_postcode ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12">
                <dl class="butiran-medan">
                    <dt>Alamat</dt>
                    <dd>
                        {{ collect([
                            $bangunan->asb_add1,
                            $bangunan->asb_add2,
                            $bangunan->asb_postcode,
                            $bangunan->asb_city,
                            $bangunan->asb_state,
                            $bangunan->asb_country,
                        ])->filter()->implode(', ') ?: '-' }}
                    </dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Daerah</dt>
                    <dd>{{ $bangunan->asb_distinct ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Mukim</dt>
                    <dd>{{ $bangunan->asb_mukim ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Negeri</dt>
                    <dd>{{ $bangunan->asb_state ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="2" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Lokasi & Agensi</h6>
            <p>Koordinat, pautan peta dan maklumat agensi berkaitan.</p>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <dl class="butiran-medan">
                    <dt>Pautan Google Maps</dt>
                    <dd>
                        @if ($bangunan->asb_gmaps_location)
                            <a href="{{ $bangunan->asb_gmaps_location }}" target="_blank" rel="noopener">
                                <i class="bi bi-geo-alt-fill me-1"></i>Buka lokasi Google Maps
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Latitud</dt>
                    <dd>{{ $bangunan->asb_latitude ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Longitud</dt>
                    <dd>{{ $bangunan->asb_longitude ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Agensi</dt>
                    <dd>{{ $bangunan->asb_agency ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Kementerian</dt>
                    <dd>{{ $bangunan->asb_ministry ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Jabatan</dt>
                    <dd>{{ $bangunan->asb_dept ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Kod PTJ</dt>
                    <dd>{{ $bangunan->asb_ptj_code ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="3" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Maklumat Fizikal</h6>
            <p>Ciri fizikal bangunan dan maklumat akaun utiliti.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Keluasan Bangunan</dt>
                    <dd>{{ $bangunan->asb_size !== null ? number_format($bangunan->asb_size, 2) . ' m²' : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Jumlah Blok</dt>
                    <dd>{{ $bangunan->asb_total_block ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Struktur Luaran</dt>
                    <dd>{{ $bangunan->asb_total_ext_structure ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>No. Akaun TNB</dt>
                    <dd>{{ $bangunan->asb_tnb_acc_no ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>No. Akaun SATU</dt>
                    <dd>{{ $bangunan->asb_satu_acc_no ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12">
                <dl class="butiran-medan">
                    <dt>Status Aset Warisan</dt>
                    <dd>{{ $bangunan->asb_aset_warisan ? 'Ya, aset warisan' : 'Bukan aset warisan' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="4" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Kos & Penilaian</h6>
            <p>Maklumat kos pembinaan, pembiayaan dan nilai semasa aset.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Kos Siap Bina Asal</dt>
                    <dd>RM {{ $bangunan->asb_kos_siap_bina_asal !== null ? number_format($bangunan->asb_kos_siap_bina_asal, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Kos Tambahan</dt>
                    <dd>RM {{ $bangunan->asb_kos_tambahan !== null ? number_format($bangunan->asb_kos_tambahan, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Jumlah Kos</dt>
                    <dd>RM {{ $bangunan->asb_total_kos !== null ? number_format($bangunan->asb_total_kos, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Tarikh Siap Bina</dt>
                    <dd>{{ $bangunan->asb_completion_date?->format('d/m/Y') ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Sumber Pembiayaan</dt>
                    <dd>{{ $bangunan->asb_financing_source ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Nilai Semasa</dt>
                    <dd>RM {{ $bangunan->asb_current_value !== null ? number_format($bangunan->asb_current_value, 2) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Tarikh Penilaian</dt>
                    <dd>{{ $bangunan->asb_assessment_date?->format('d/m/Y') ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="5" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Status & Penyelenggaraan</h6>
            <p>Status semasa aset serta rekod pemeriksaan penyelenggaraan.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Status Penyelenggaraan</dt>
                    <dd>{{ $bangunan->asb_maintenance_status ? \App\Support\Paparan::label($bangunan->asb_maintenance_status) : '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="butiran-medan">
                    <dt>Status Premis</dt>
                    <dd>{{ $bangunan->asb_premise_status ? \App\Support\Paparan::label($bangunan->asb_premise_status) : '-' }}</dd>
                </dl>
            </div>

            {{-- REKOD PEMERIKSAAN PENYELENGGARAAN --}}
            @if ($bolehLihatPemeriksaan ?? false)
                @php
                    $senaraiPemeriksaan = $bangunan->penyelenggaraan;
                    $pemeriksaanTerkini = $senaraiPemeriksaan->first();
                    $sejarahPemeriksaan = $senaraiPemeriksaan->slice(1);
                @endphp

                <div class="col-12">
                    <h6 class="fw-bold mt-2 mb-0">
                        Pemeriksaan Penyelenggaraan Terkini
                    </h6>
                </div>

                <div class="col-12">
                    @if ($pemeriksaanTerkini)
                        <div class="border rounded-3 p-3">
                            @include('bangunan._pemeriksaan', ['rekod' => $pemeriksaanTerkini])
                        </div>
                    @else
                        <div class="kad-kosong py-4">
                            <div class="ilustrasi-kosong">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                            <h3>Tiada rekod pemeriksaan</h3>
                            <p>Pemeriksaan penyelenggaraan belum direkodkan bagi aset ini.</p>
                        </div>
                    @endif
                </div>

                @if ($sejarahPemeriksaan->isNotEmpty())
                    <div class="col-12">
                        <h6 class="fw-bold mt-2 mb-2">
                            Sejarah Pemeriksaan
                            <small class="text-secondary fw-normal">({{ $sejarahPemeriksaan->count() }} rekod)</small>
                        </h6>

                        <div class="accordion" id="sejarahPemeriksaan{{ $bangunan->asb_id }}">
                            @foreach ($sejarahPemeriksaan as $rekod)
                                @php
                                    $idRekod = 'pemeriksaan' . $rekod->asm_id;
                                @endphp

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button
                                            class="accordion-button collapsed py-2 small"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $idRekod }}"
                                            aria-expanded="false"
                                            aria-controls="{{ $idRekod }}"
                                        >
                                            <span class="me-auto">
                                                <strong>{{ $rekod->asm_inspection_date->format('d/m/Y') }}</strong>
                                                <span class="text-secondary ms-2">
                                                    oleh {{ $rekod->pemeriksa?->usr_name ?? '-' }}
                                                </span>
                                            </span>

                                            @php
                                                $rosak = $rekod->ringkasanNilai()['ROSAK'];
                                            @endphp
                                            <span class="badge rounded-pill me-2 {{ $rosak > 0 ? 'bg-danger-subtle text-danger-emphasis' : 'bg-success-subtle text-success-emphasis' }}">
                                                {{ $rosak > 0 ? "{$rosak} rosak" : 'Tiada kerosakan' }}
                                            </span>
                                        </button>
                                    </h2>

                                    <div
                                        id="{{ $idRekod }}"
                                        class="accordion-collapse collapse"
                                        data-bs-parent="#sejarahPemeriksaan{{ $bangunan->asb_id }}"
                                    >
                                        <div class="accordion-body">
                                            @include('bangunan._pemeriksaan', ['rekod' => $rekod])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="6" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Pengumpulan & Pengesahan</h6>
            <p>Maklumat pegawai yang mengumpul dan mengesahkan data aset.</p>
        </div>

        <div class="row g-3">
            <div class="col-12"><h6 class="fw-bold mt-2 mb-0">Maklumat Pengumpulan</h6></div>

            <div class="col-12 col-md-5">
                <dl class="butiran-medan">
                    <dt>Nama Pegawai Pengumpul</dt>
                    <dd>{{ $bangunan->asb_collector_name ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Jawatan Pengumpul</dt>
                    <dd>{{ $bangunan->asb_collector_position ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-3">
                <dl class="butiran-medan">
                    <dt>Tarikh Dikumpul</dt>
                    <dd>{{ $bangunan->asb_collected_at?->format('d/m/Y') ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12"><h6 class="fw-bold mt-2 mb-0">Maklumat Pengesahan</h6></div>

            <div class="col-12 col-md-5">
                <dl class="butiran-medan">
                    <dt>Nama Pegawai Pengesah</dt>
                    <dd>{{ $bangunan->asb_verifier_name ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-4">
                <dl class="butiran-medan">
                    <dt>Jawatan Pengesah</dt>
                    <dd>{{ $bangunan->asb_verifier_position ?: '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-3">
                <dl class="butiran-medan">
                    <dt>Tarikh Disahkan</dt>
                    <dd>{{ $bangunan->asb_verified_at?->format('d/m/Y') ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="7" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Maklumat Penyewa</h6>
            <p>{{ $bangunan->penyewa->count() }} rekod penyewa bagi aset ini.</p>
        </div>

        @forelse ($bangunan->penyewa as $penyewa)
            <div class="butiran-medan mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <strong>{{ $penyewa->tnt_name }}</strong>
                    <span class="lencana-jadual hijau">
                        {{ $penyewa->tnt_status ? \App\Support\Paparan::label($penyewa->tnt_status) : '-' }}
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <small class="teks-kecil d-block">No. Kad Pengenalan / Syarikat</small>
                        <span>{{ $penyewa->tnt_ic_no ?: '-' }}</span>
                    </div>

                    <div class="col-12 col-md-4">
                        <small class="teks-kecil d-block">No. Akaun Penyewa</small>
                        <span>{{ $penyewa->tnt_acc_no ?: '-' }}</span>
                    </div>

                    <div class="col-12 col-md-4">
                        <small class="teks-kecil d-block">Tempoh Perjanjian</small>
                        <span>
                            {{ $penyewa->tnt_mula_perjanjian?->format('d/m/Y') ?? '-' }}
                            -
                            {{ $penyewa->tnt_tamat_perjanjian?->format('d/m/Y') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-secondary">
                <i class="bi bi-people fs-3 d-block mb-2"></i>
                Tiada penyewa direkodkan.
            </div>
        @endforelse
    </section>

    <section class="panel-butiran" data-tab-butiran-panel="8" role="tabpanel" hidden>
        <div class="butiran-seksyen">
            <h6>Dokumen Aset Bangunan</h6>
            <p>Dokumen sokongan dan Kod QR aduan bagi aset ini.</p>
        </div>

        @forelse ($bangunan->dokumen as $dokumen)
            <a
                href="{{ route('bangunan.dokumen.lihat', [$bangunan, $dokumen]) }}"
                target="_blank"
                rel="noopener"
                class="d-flex align-items-center gap-3 border rounded-3 p-3 mb-2 text-decoration-none"
            >
                <div class="ikon-dokumen">
                    <i class="bi bi-file-earmark-text"></i>
                </div>

                <div class="min-w-0">
                    <strong class="d-block text-dark text-truncate">{{ $dokumen->asbdoc_name }}</strong>
                    <small class="text-secondary">
                        {{ \App\Support\Paparan::label($dokumen->asbdoc_type) }}
                        · {{ $dokumen->uploaded_at?->format('d/m/Y') ?? '-' }}
                    </small>
                </div>

                <i class="bi bi-box-arrow-up-right ms-auto text-primary"></i>
            </a>
        @empty
            <p class="text-secondary">Tiada dokumen direkodkan.</p>
        @endforelse

        <hr class="my-4">

        @if ($bangunan->kod)
            @php
                $pautanAduan = rtrim((string) config('app.url'), '/')
                    . '/laporan/'
                    . $bangunan->kod->qr_code_value;
            @endphp

            <div class="kad-qr-butiran">
                <div class="qr-butiran-imej">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(180)
                        ->margin(1)
                        ->generate($pautanAduan) !!}
                </div>

                <div class="qr-butiran-kandungan">
                    <span class="qr-butiran-label">
                        <i class="bi bi-qr-code-scan"></i> KOD QR ADUAN
                    </span>

                    <h6 class="mt-2 mb-1 fw-bold">Aduan Aset Bangunan</h6>

                    <p class="text-secondary small mb-3">
                        Imbas kod ini untuk membuat aduan berkaitan
                        <strong>{{ $bangunan->asb_name }}</strong>.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('kodqr.show', $bangunan) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-qr-code me-1"></i>Urus Kod QR
                        </a>

                        <a href="{{ $pautanAduan }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Pautan Aduan
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </section>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
        Tutup
    </button>

    <a href="{{ route('bangunan.edit', $bangunan) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i>Kemaskini
    </a>
</div>

<script>
    (() => {
        const label = document.currentScript
            ?.previousElementSibling
            ?.closest('.modal-body, body')
            ?.querySelector('[data-label-tab-aktif]')
            ?? document.querySelector('[data-label-tab-aktif]');

        if (! label) {
            return;
        }

        const bekas = label.closest('.tab-butiran-melekat') ?? label.parentElement;

        const kemaskini = () => {
            const aktif = bekas.querySelector('.tab-butiran-butang.aktif');

            if (aktif) {
                label.textContent = aktif.dataset.label;
            }
        };

        new MutationObserver(kemaskini).observe(bekas, {
            subtree: true,
            attributes: true,
            attributeFilter: ['class'],
        });

        kemaskini();
    })();
</script>