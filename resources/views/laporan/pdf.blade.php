<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 22px 22px 42px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111111;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }

        .kepala-laporan {
            border-bottom: 2px solid #111111;
            margin-bottom: 15px;
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

        .maklumat-kepala {
            width: 78%;
        }

        .logo-kepala {
            text-align: right;
            white-space: nowrap;
            width: 22%;
        }

        .logo-kepala img {
            display: inline-block;
            height: auto;
            margin-left: 10px;
            max-height: 44px;
            max-width: 82px;
            object-fit: contain;
            vertical-align: middle;
        }

        .jenama {
            color: #444444;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.15px;
            margin-bottom: 5px;
        }

        .tajuk-laporan {
            color: #111111;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.2px;
            margin: 0;
        }

        .penerangan-kepala {
            color: #555555;
            font-size: 8px;
            margin-top: 5px;
        }

        .jadual-laporan {
            border-collapse: collapse;
            width: 100%;
        }

        .jadual-laporan thead {
            display: table-header-group;
        }

        .jadual-laporan tr {
            page-break-inside: avoid;
        }

        .jadual-laporan th {
            background: #333333;
            border: 1px solid #333333;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 7px 6px;
            text-align: left;
            vertical-align: middle;
        }

        .jadual-laporan td {
            border: 1px solid #cccccc;
            color: #111111;
            padding: 7px 6px;
            vertical-align: top;
        }

        .jadual-laporan tbody tr:nth-child(even) td {
            background: #f5f5f5;
        }

        .tiada-rekod {
            color: #555555;
            padding: 16px !important;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="kepala-laporan">
        <table class="jadual-kepala">
            <tr>
                <td class="maklumat-kepala">
                    <div class="jenama">
                        MPAS · MARANG PROPERTY ASSET SYSTEM
                    </div>

                    <h1 class="tajuk-laporan">{{ $tajuk }}</h1>

                    <div class="penerangan-kepala">
                        Dijana pada {{ $tarikh->format('d/m/Y, h:i A') }}
                    </div>
                </td>

                <td class="logo-kepala">
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

    <table class="jadual-laporan">
        <thead>
            <tr>
                @foreach ($kepala as $tajukKolum)
                    <th>{{ $tajukKolum }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse ($baris as $barisData)
                <tr>
                    @foreach ($barisData as $nilai)
                        <td>{{ $nilai }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td
                        colspan="{{ count($kepala) }}"
                        class="tiada-rekod"
                    >
                        Tiada rekod ditemui.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>