<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EksportLaporan implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly array $kepala,
        private readonly array $baris,
        private readonly string $tajuk,
    ) {
    }

    public function collection(): Collection
    {
        return collect($this->baris);
    }

    public function headings(): array
    {
        return $this->kepala;
    }

    public function title(): string
    {
        return 'Laporan MPAS';
    }

    public function styles(Worksheet $helaian): array
    {
        $helaian->freezePane('A2');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '168A7E'],
                ],
            ],
        ];
    }
}