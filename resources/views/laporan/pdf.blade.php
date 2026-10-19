<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 22px;
        }

        body {
            color: #1d2b2a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        .kepala {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #168a7e;
        }

        .kepala h1 {
            margin: 0 0 5px;
            color: #0d625a;
            font-size: 18px;
        }

        .kepala p {
            margin: 0;
            color: #667775;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 8px 6px;
            background: #168a7e;
            color: #ffffff;
            font-size: 8px;
            text-align: left;
        }

        td {
            padding: 7px 6px;
            border-bottom: 1px solid #dce9e7;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #f5faf9;
        }

        .kaki {
            margin-top: 15px;
            color: #718280;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="kepala">
        <h1>{{ $tajuk }}</h1>
        <p>
            Marang Property Asset System (MPAS)
            · Dijana pada {{ $tarikh->format('d/m/Y, h:i A') }}
        </p>
    </div>

    <table>
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
                    <td colspan="{{ count($kepala) }}" style="text-align: center;">
                        Tiada rekod ditemui.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="kaki">
        Dokumen ini dijana oleh MPAS untuk kegunaan rasmi Majlis Daerah Marang.
    </p>
</body>
</html>