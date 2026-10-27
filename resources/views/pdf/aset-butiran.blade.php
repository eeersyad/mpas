<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 20px 24px 42px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111111;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            line-height: 1.45;
        }

        .kepala {
            border-bottom: 2px solid #111111;
            margin-bottom: 16px;
            padding-bottom: 10px;
        }

        .jadual-kepala {
            border-collapse: collapse;
            width: 100%;
        }

        .jadual-kepala td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }

        .maklumat-kepala-pdf {
            width: 78%;
        }

        .logo-kepala-pdf {
            text-align: right;
            white-space: nowrap;
            width: 22%;
        }

        .logo-kepala-pdf img {
            display: inline-block;
            height: auto;
            margin-left: 10px;
            max-height: 44px;
            max-width: 82px;
            object-fit: contain;
            vertical-align: middle;
        }

        .jenama {
            color: #333333;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1.2px;
            margin-bottom: 5px;
        }

        .tajuk {
            color: #111111;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.2px;
            margin: 0;
        }

        .maklumat-kepala {
            color: #555555;
            font-size: 8px;
            margin-top: 5px;
        }

        .seksyen {
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .tajuk-seksyen {
            background: #eeeeee;
            border-left: 4px solid #111111;
            color: #111111;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.3px;
            padding: 6px 8px;
        }

        .jadual-butiran,
        .jadual-tiga,
        .jadual-penyewa {
            border-collapse: collapse;
            margin-top: 5px;
            width: 100%;
        }

        .jadual-butiran td,
        .jadual-tiga td {
            border: 1px solid #c9c9c9;
            padding: 6px 7px;
            vertical-align: top;
        }

        .label {
            background: #f2f2f2;
            color: #333333;
            font-size: 8px;
            font-weight: bold;
        }

        .jadual-butiran .label {
            width: 17%;
        }

        .jadual-butiran .nilai {
            width: 33%;
        }

        .jadual-tiga .label {
            width: 12%;
        }

        .jadual-tiga .nilai {
            width: 21.33%;
        }

        .nilai {
            color: #111111;
            word-wrap: break-word;
        }

        .jadual-penyewa th {
            background: #333333;
            color: #ffffff;
            font-size: 8px;
            padding: 7px;
            text-align: left;
        }

        .jadual-penyewa td {
            border-bottom: 1px solid #c9c9c9;
            padding: 7px;
            vertical-align: top;
        }

        .jadual-penyewa tr:nth-child(even) td {
            background: #f6f6f6;
        }

        .jadual-pengesahan {
            border-collapse: collapse;
            margin-top: 5px;
            width: 100%;
        }

        .jadual-pengesahan td {
            border: 1px solid #c9c9c9;
            padding: 6px 7px;
            vertical-align: top;
        }

        .jadual-pengesahan .label {
            background: #f2f2f2;
            color: #333333;
            font-size: 8px;
            font-weight: bold;
            width: 13%;
        }

        .jadual-pengesahan .nilai {
            color: #111111;
            width: 20.33%;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    @php
        $papar = static fn ($nilai) => filled($nilai) ? $nilai : '-';

        $enum = static fn ($nilai) => filled($nilai)
            ? \App\Support\Paparan::label($nilai)
            : '-';

        $wang = static fn ($nilai) => $nilai !== null && $nilai !== ''
            ? 'RM ' . number_format((float) $nilai, 2)
            : '-';

        $tarikhMedan = static fn ($nilai) => $nilai
            ? $nilai->format('d/m/Y')
            : '-';

        if ($jenis === 'bangunan') {
            $alamat = collect([
                $aset->asb_add1,
                $aset->asb_add2,
                $aset->asb_postcode,
                $aset->asb_city,
                $aset->asb_state,
                $aset->asb_country,
            ])->filter()->implode(', ');

            $tajukDokumen = 'Maklumat Aset Bangunan';
            $rujukan = $aset->asb_ref_no;

            $seksyen = [
                [
                    'tajuk' => 'Maklumat Asas',
                    'medan' => [
                        ['No. Rujukan', $papar($aset->asb_ref_no)],
                        ['Nama Premis', $papar($aset->asb_name)],
                        ['No. Fail', $papar($aset->asb_no_file)],
                        ['Kategori', $enum($aset->asb_category)],
                        ['Subkategori', $papar($aset->asb_sub_category)],
                        ['Alamat Penuh', $papar($alamat)],
                        ['Poskod', $papar($aset->asb_postcode)],
                        ['Daerah', $papar($aset->asb_distinct)],
                        ['Mukim', $papar($aset->asb_mukim)],
                        ['Bandar', $papar($aset->asb_city)],
                        ['Negeri', $papar($aset->asb_state)],
                        ['Negara', $papar($aset->asb_country)],
                    ],
                ],
                [
                    'tajuk' => 'Lokasi dan Agensi',
                    'medan' => [
                        ['Pautan Google Maps', $papar($aset->asb_gmaps_location)],
                        ['Latitud', $papar($aset->asb_latitude)],
                        ['Longitud', $papar($aset->asb_longitude)],
                        ['Agensi', $papar($aset->asb_agency)],
                        ['Kementerian', $papar($aset->asb_ministry)],
                        ['Jabatan', $papar($aset->asb_dept)],
                        ['Kod PTJ', $papar($aset->asb_ptj_code)],
                    ],
                ],
                [
                    'tajuk' => 'Maklumat Fizikal',
                    'medan' => [
                        [
                            'Keluasan Bangunan',
                            $aset->asb_size !== null
                                ? number_format($aset->asb_size, 2) . ' m²'
                                : '-',
                        ],
                        ['Jumlah Blok', $papar($aset->asb_total_block)],
                        ['Struktur Luaran', $papar($aset->asb_total_ext_structure)],
                        ['No. Akaun TNB', $papar($aset->asb_tnb_acc_no)],
                        ['No. Akaun SATU', $papar($aset->asb_satu_acc_no)],
                        [
                            'Aset Warisan',
                            $aset->asb_aset_warisan === null
                                ? '-'
                                : ($aset->asb_aset_warisan ? 'Ya' : 'Tidak'),
                        ],
                    ],
                ],
                [
                    'tajuk' => 'Kos dan Penilaian',
                    'medan' => [
                        ['Kos Siap Bina Asal', $wang($aset->asb_kos_siap_bina_asal)],
                        ['Kos Tambahan', $wang($aset->asb_kos_tambahan)],
                        ['Jumlah Kos', $wang($aset->asb_total_kos)],
                        ['Tarikh Siap Bina', $tarikhMedan($aset->asb_completion_date)],
                        ['Sumber Pembiayaan', $papar($aset->asb_financing_source)],
                        ['Nilai Semasa', $wang($aset->asb_current_value)],
                        ['Tarikh Penilaian', $tarikhMedan($aset->asb_assessment_date)],
                    ],
                ],
                [
                    'tajuk' => 'Status & Pengesahan',
                    'jenis' => 'pengesahan',
                ],
            ];
        } else {
            $tajukDokumen = 'Maklumat Aset Tanah';
            $rujukan = $aset->asl_ref_no;

            $seksyen = [
                [
                    'tajuk' => 'Maklumat Tanah',
                    'medan' => [
                        ['No. Rujukan', $papar($aset->asl_ref_no)],
                        ['No. Lot', $papar($aset->asl_lot_no)],
                        ['No. Fail', $papar($aset->asl_no_file)],
                        ['Lokasi Tanah', $papar($aset->asl_location)],
                        [
                            'Keluasan Tanah',
                            $aset->asl_land_size !== null
                                ? number_format($aset->asl_land_size, 2) . ' m²'
                                : '-',
                        ],
                    ],
                ],
                [
                    'tajuk' => 'Maklumat Hakmilik',
                    'medan' => [
                        ['Nombor Hakmilik', $papar($aset->asl_hakmilik_no)],
                        ['Status Hakmilik', $enum($aset->asl_status_hakmilik)],
                        ['Status Tanah', $enum($aset->asl_status_tanah)],
                        ['Tarikh Terima Hakmilik', $tarikhMedan($aset->asl_tarikh_terima_hakmilik)],
                        ['Tarikh Serah Balik Hakmilik', $tarikhMedan($aset->asl_tarikh_serah_balik_hakmilik)],
                    ],
                ],
                [
                    'tajuk' => 'Lokasi Peta',
                    'medan' => [
                        ['Pautan Google Maps', $papar($aset->asl_gmaps_location)],
                        ['Latitud', $papar($aset->asl_latitude)],
                        ['Longitud', $papar($aset->asl_longitude)],
                    ],
                ],
                [
                    'tajuk' => 'Nilai dan Cukai',
                    'medan' => [
                        ['Harga Tanah', $wang($aset->asl_land_price)],
                        ['SKP', $wang($aset->asl_land_skp)],
                        ['Cukai Tanah', $wang($aset->asl_cukaiT)],
                        ['Status Cukai Tanah', $papar($aset->asl_status_cukaiT)],
                    ],
                ],
            ];
        }
    @endphp

    <div class="kepala">
        <table class="jadual-kepala">
            <tr>
                <td class="maklumat-kepala-pdf">
                    <div class="jenama">
                        MPAS · MARANG PROPERTY ASSET SYSTEM
                    </div>

                    <h1 class="tajuk">{{ $tajukDokumen }}</h1>

                    <div class="maklumat-kepala">
                        No. Rujukan: {{ $rujukan }}
                        &nbsp;|&nbsp;
                        Dijana pada: {{ $tarikh->format('d/m/Y, h:i A') }}
                    </div>
                </td>

                <td class="logo-kepala-pdf">
                    <img
                        src="data:image/png;base64,{{ $logoMdm }}"
                        alt="Logo Majlis Daerah Marang"
                    >

                    <img
                        src="data:image/png;base64,{{ $logoMpas }}"
                        alt="Logo MPAS"
                    >
                </td>
            </tr>
        </table>
    </div>

    @foreach ($seksyen as $seksyenItem)
        <div class="seksyen">
            <div class="tajuk-seksyen">{{ $seksyenItem['tajuk'] }}</div>

            @if (($seksyenItem['jenis'] ?? null) === 'pengesahan')
                <table class="jadual-pengesahan">
                    <tbody>
                        <tr>
                            <td class="label">Status Premis</td>
                            <td class="nilai" colspan="2">
                                {{ $enum($aset->asb_premise_status) }}
                            </td>

                            <td class="label">Status Penyelenggaraan</td>
                            <td class="nilai" colspan="2">
                                {{ $enum($aset->asb_maintenance_status) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="label">Nama Pegawai Pengumpul</td>
                            <td class="nilai">{{ $papar($aset->asb_collector_name) }}</td>

                            <td class="label">Jawatan Pengumpul</td>
                            <td class="nilai">{{ $papar($aset->asb_collector_position) }}</td>

                            <td class="label">Tarikh Dikumpul</td>
                            <td class="nilai">{{ $tarikhMedan($aset->asb_collected_at) }}</td>
                        </tr>

                        <tr>
                            <td class="label">Nama Pegawai Pengesah</td>
                            <td class="nilai">{{ $papar($aset->asb_verifier_name) }}</td>

                            <td class="label">Jawatan Pengesah</td>
                            <td class="nilai">{{ $papar($aset->asb_verifier_position) }}</td>

                            <td class="label">Tarikh Disahkan</td>
                            <td class="nilai">{{ $tarikhMedan($aset->asb_verified_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <table class="jadual-butiran">
                    <tbody>
                        @foreach (array_chunk($seksyenItem['medan'], 2) as $baris)
                            <tr>
                                @foreach ($baris as [$label, $nilai])
                                    <td class="label">{{ $label }}</td>
                                    <td class="nilai">{{ $nilai }}</td>
                                @endforeach

                                @if (count($baris) === 1)
                                    <td class="label"></td>
                                    <td class="nilai"></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    @if ($jenis === 'bangunan')
        <div class="seksyen">
            <div class="tajuk-seksyen">Maklumat Penyewa</div>

            <table class="jadual-penyewa">
                <thead>
                    <tr>
                        <th>Bil.</th>
                        <th>Nama Penyewa</th>
                        <th>No. Kad Pengenalan / Syarikat</th>
                        <th>No. Akaun</th>
                        <th>Tempoh Sewa</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($aset->penyewa as $penyewa)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $papar($penyewa->tnt_name) }}</td>
                            <td>{{ $papar($penyewa->tnt_ic_no) }}</td>
                            <td>{{ $papar($penyewa->tnt_acc_no) }}</td>
                            <td>
                                {{ $tarikhMedan($penyewa->tnt_start_date) }}
                                hingga
                                {{ $tarikhMedan($penyewa->tnt_end_date) }}
                            </td>
                            <td>{{ $enum($penyewa->tnt_status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #555555;">
                                Tiada penyewa direkodkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>