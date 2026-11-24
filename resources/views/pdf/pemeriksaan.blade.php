<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">

    <style>
        @page { margin: 22px 26px 44px; }
        * { box-sizing: border-box; }
        body { color: #111111; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.45; }

        .kepala { border-bottom: 2px solid #111111; margin-bottom: 14px; padding-bottom: 10px; }
        .jadual-kepala { border-collapse: collapse; width: 100%; }
        .jadual-kepala td { border: 0; padding: 0; vertical-align: middle; }
        .logo-kepala-pdf { text-align: right; white-space: nowrap; width: 30%; }
        .logo-kepala-pdf img { display: inline-block; margin-left: 8px; max-height: 40px; max-width: 76px; vertical-align: middle; }
        .jenama { color: #333333; font-size: 8px; font-weight: bold; letter-spacing: 1.2px; margin-bottom: 4px; }
        .tajuk { font-size: 16px; font-weight: bold; margin: 0; }
        .maklumat-kepala { color: #555555; font-size: 8px; margin-top: 4px; }

        .seksyen { margin-top: 12px; }
        .rekod-pemeriksaan { margin-top: 14px; }
        .rekod-pemeriksaan + .rekod-pemeriksaan { page-break-before: always; margin-top: 0; }
        .tajuk-seksyen { background: #eeeeee; border-left: 4px solid #111111; font-size: 10px; font-weight: bold; padding: 6px 8px; }
        .tajuk-kecil { margin: 10px 0 0; font-size: 9px; font-weight: bold; color: #333333; }

        .jadual-butiran, .jadual-item, .jadual-tandatangan, .jadual-gambar { border-collapse: collapse; margin-top: 5px; width: 100%; }
        .jadual-butiran td { border: 1px solid #c9c9c9; padding: 5px 7px; vertical-align: top; }
        .label { background: #f2f2f2; color: #333333; font-size: 8px; font-weight: bold; width: 20%; }
        .nilai { width: 30%; word-wrap: break-word; }

        .jadual-item th { background: #333333; color: #ffffff; font-size: 8px; padding: 6px; text-align: left; }
        .jadual-item td { border-bottom: 1px solid #c9c9c9; padding: 5px 6px; vertical-align: top; }
        .keputusan-baik { color: #146c43; font-weight: bold; }
        .keputusan-rosak { color: #b02a37; font-weight: bold; }
        .keputusan-tidak_berkaitan { color: #555555; }

        .jadual-gambar td { width: 50%; padding: 6px; border: 1px solid #c9c9c9; text-align: center; vertical-align: top; }
        .jadual-gambar tr { page-break-inside: avoid; }
        .bingkai-gambar { height: 182px; }
        .nama-gambar { margin-top: 5px; font-weight: bold; font-size: 8.5px; text-align: left; }
        .ringkasan-gambar { margin-top: 2px; color: #555555; font-size: 8px; text-align: left; }

        .jadual-tandatangan { page-break-inside: avoid; }
        .jadual-tandatangan td { border: 1px solid #c9c9c9; padding: 6px 7px; vertical-align: top; width: 50%; }
        .ruang-tandatangan { height: 80px; }
        .baris-tandatangan { color: #333333; font-size: 8px; line-height: 1.9; }
        .kosong { color: #555555; margin: 6px 0 0; }
    </style>
</head>

<body>
    @php
        $papar = static fn ($nilai) => filled($nilai) ? $nilai : '-';
        $enum = static fn ($nilai) => filled($nilai) ? \App\Support\Paparan::label($nilai) : '-';
        $tarikhMedan = static fn ($nilai) => $nilai ? $nilai->format('d/m/Y') : '-';
    @endphp

    <div class="kepala">
        <table class="jadual-kepala">
            <tr>
                <td>
                    <div class="jenama">MPAS · MARANG PROPERTY ASSET SYSTEM</div>
                    <h1 class="tajuk">Maklumat Pemeriksaan Penyelenggaraan</h1>
                    <div class="maklumat-kepala">
                        No. Rujukan: {{ $aset->asb_ref_no }} &nbsp;|&nbsp; Dijana pada: {{ $tarikh->format('d/m/Y, h:i A') }}
                    </div>
                </td>
                <td class="logo-kepala-pdf">
                    <img src="data:image/png;base64,{{ $logoMdm }}" alt="Logo Majlis Daerah Marang">
                    <img src="data:image/png;base64,{{ $logoMpas }}" alt="Logo MPAS">
                </td>
            </tr>
        </table>
    </div>

    <div class="tajuk-seksyen">Maklumat Aset</div>
    <table class="jadual-butiran">
        <tbody>
            <tr>
                <td class="label">Nama Premis</td>
                <td class="nilai" colspan="3">{{ $papar($aset->asb_name) }}</td>
            </tr>
            <tr>
                <td class="label">No. Rujukan</td>
                <td class="nilai">{{ $papar($aset->asb_ref_no) }}</td>
                <td class="label">Mukim</td>
                <td class="nilai">{{ $papar($aset->asb_mukim) }}</td>
            </tr>
            <tr>
                <td class="label">Status Penyelenggaraan</td>
                <td class="nilai">{{ $enum($aset->asb_maintenance_status) }}</td>
                <td class="label">Jumlah Pemeriksaan</td>
                <td class="nilai">{{ $senaraiRekod->count() }}</td>
            </tr>
        </tbody>
    </table>

    @forelse ($senaraiRekod as $rekod)
        @php
            $pemeriksaan = $rekod['pemeriksaan'];
        @endphp

        <div class="rekod-pemeriksaan">
            <div class="tajuk-seksyen">
                Pemeriksaan {{ $tarikhMedan($pemeriksaan->asm_inspection_date) }}@if ($loop->first) &nbsp;(Terkini)@endif
            </div>

            <table class="jadual-butiran">
                <tbody>
                    <tr>
                        <td class="label">Tarikh Pemeriksaan</td>
                        <td class="nilai">{{ $tarikhMedan($pemeriksaan->asm_inspection_date) }}</td>
                        <td class="label">Ringkasan</td>
                        <td class="nilai">
                            @foreach ($rekod['ringkasan'] as $nilai => $jumlah)
                                {{ $enum($nilai) }}: <strong>{{ $jumlah }}</strong>@if (! $loop->last)<br>@endif
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="jadual-item">
                <thead>
                    <tr>
                        <th style="width: 6%;">Bil.</th>
                        <th style="width: 30%;">Komponen</th>
                        <th>Item</th>
                        <th style="width: 20%;">Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemeriksaan->asm_items ?? [] as $komponen)
                        @foreach ($komponen['item'] ?? [] as $item)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ count($komponen['item']) }}">{{ $loop->parent->iteration }}</td>
                                    <td rowspan="{{ count($komponen['item']) }}">{{ $papar($komponen['nama'] ?? null) }}</td>
                                @endif
                                <td>{{ $papar($item['nama'] ?? null) }}</td>
                                <td class="keputusan-{{ strtolower($item['nilai'] ?? 'tiada') }}">{{ $enum($item['nilai'] ?? null) }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #555555;">Tiada keputusan item direkodkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (filled($pemeriksaan->asm_note))
                <table class="jadual-butiran" style="margin-top: 6px;">
                    <tbody>
                        <tr>
                            <td class="label">Catatan Keseluruhan</td>
                            <td class="nilai" style="width: 80%;">{!! nl2br(e($pemeriksaan->asm_note)) !!}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <p class="tajuk-kecil">Gambar Pemeriksaan ({{ count($rekod['gambar']) }})</p>

            @if (count($rekod['gambar']))
                <table class="jadual-gambar">
                    <tbody>
                        @foreach (array_chunk($rekod['gambar'], 2) as $baris)
                            <tr>
                                @foreach ($baris as $gambar)
                                    <td>
                                        <div class="bingkai-gambar">
                                            <img src="data:image/jpeg;base64,{{ $gambar['data'] }}" width="{{ $gambar['lebar'] }}" height="{{ $gambar['tinggi'] }}" alt="{{ $gambar['nama'] }}">
                                        </div>
                                        <div class="nama-gambar">{{ $gambar['nama'] }}</div>
                                        @if (filled($gambar['ringkasan']))
                                            <div class="ringkasan-gambar">{{ $gambar['ringkasan'] }}</div>
                                        @endif
                                    </td>
                                @endforeach

                                @if (count($baris) === 1)
                                    <td style="border: 0;"></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="kosong">Tiada gambar bagi pemeriksaan ini.</p>
            @endif

            <table class="jadual-tandatangan" style="margin-top: 12px;">
                <tbody>
                    <tr>
                        <td class="label" style="width: 50%;">Disediakan oleh</td>
                        <td class="label" style="width: 50%;">Disemak oleh</td>
                    </tr>
                    <tr>
                        <td class="ruang-tandatangan"></td>
                        <td class="ruang-tandatangan"></td>
                    </tr>
                    <tr>
                        <td class="baris-tandatangan">Nama:<br>Jawatan:<br>Tarikh:</td>
                        <td class="baris-tandatangan">Nama:<br>Jawatan:<br>Tarikh:</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <div class="seksyen">
            <div class="tajuk-seksyen">Rekod Pemeriksaan</div>
            <p class="kosong">Tiada rekod pemeriksaan penyelenggaraan bagi aset ini.</p>
        </div>
    @endforelse
</body>
</html>
