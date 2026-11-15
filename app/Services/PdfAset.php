<?php

namespace App\Services;

use App\Support\Paparan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

/**
 * Jana PDF maklumat aset (bangunan / tanah) berserta gambar aset,
 * dan ZIP (PDF maklumat + semua dokumen asal).
 */
class PdfAset
{
    /** Ruang maksimum gambar dalam PDF (px, A4 landskap). */
    private const LEBAR_MAKS = 980;
    private const TINGGI_MAKS = 440;

    /** Fail sementara yang perlu dibuang selepas jana. */
    private array $failSementara = [];

    /**
     * PDF maklumat aset + gambar aset (dokumen jenis GAMBAR). Pulangkan kandungan PDF.
     */
    public function janaMaklumat(string $jenis, Model $aset): string
    {
        if ($jenis === 'bangunan') {
            $aset->loadMissing('penyewa');
        }

        try {
            $pdf = Pdf::loadView('pdf.aset-butiran', [
                'jenis' => $jenis,
                'aset' => $aset,
                'tarikh' => now(),
                'gambarAset' => $this->gambarAset($jenis, $aset),
                'logoMdm' => base64_encode(file_get_contents(public_path('images/logo-mdm.png'))),
                'logoMpas' => base64_encode(file_get_contents(public_path('images/logo-mpas-2.png'))),
            ])->setPaper('a4', 'landscape');

            $pdf->render();

            $dompdf = $pdf->getDomPDF();
            $kanvas = $dompdf->getCanvas();
            $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');

            $kanvas->page_text(24, 570, 'Dokumen ini dijana oleh MPAS untuk kegunaan rasmi Majlis Daerah Marang.', $font, 7, [0.33, 0.33, 0.33]);
            $kanvas->page_text(742, 570, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', $font, 7, [0.33, 0.33, 0.33]);

            return $dompdf->output();
        } finally {
            $this->bersihkan();
        }
    }

    /**
     * ZIP: "00 - Maklumat Aset.pdf" + semua dokumen asal. Pulangkan laluan fail ZIP sementara.
     */
    public function janaZip(string $jenis, Model $aset): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Sambungan PHP "zip" tidak diaktifkan pada server.');
        }

        @set_time_limit(180);

        $awalan = $jenis === 'bangunan' ? 'asbdoc' : 'asldoc';
        $laluanZip = tempnam(sys_get_temp_dir(), 'mpas-zip-');
        $zip = new ZipArchive();

        if ($zip->open($laluanZip, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Fail ZIP tidak dapat dicipta.');
        }

        $zip->addFromString('00 - Maklumat Aset.pdf', $this->janaMaklumat($jenis, $aset));

        $tiada = [];

        $senarai = $aset->dokumen()->orderBy("{$awalan}_id")->get();

        foreach ($senarai->values() as $indeks => $dokumen) {
            $path = $dokumen->{"{$awalan}_path"};
            $namaAsal = $dokumen->{"{$awalan}_name"} ?: basename((string) $path);
            $laluan = $this->laluanFail($path);

            $nama = sprintf('%02d - %s', $indeks + 1, $this->namaFailSelamat($namaAsal));
            $sambungan = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

            if (! pathinfo($nama, PATHINFO_EXTENSION) && $sambungan) {
                $nama .= '.' . $sambungan;
            }

            if ($laluan) {
                $zip->addFile($laluan, $nama);
            } else {
                $tiada[] = '- ' . $namaAsal;
            }
        }

        if ($tiada) {
            $zip->addFromString(
                'FAIL-TIDAK-DIJUMPAI.txt',
                "Fail berikut direkodkan dalam sistem tetapi tidak dijumpai dalam storan:\r\n\r\n" . implode("\r\n", $tiada)
            );
        }

        $zip->close();

        return $laluanZip;
    }

    /**
     * Laluan sebenar fail dokumen (disk local, atau public untuk fail lama).
     */
    private function laluanFail(?string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if ($path && Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->path($path);
            }
        }

        return null;
    }

    private function namaFailSelamat(?string $teks): string
    {
        $bersih = preg_replace('/[\\\\\/:*?"<>|]+/', '-', (string) $teks);
        $bersih = preg_replace('/\s+/', ' ', (string) $bersih);

        return trim((string) $bersih, " -.") ?: 'dokumen';
    }

    /**
     * Semua dokumen berformat gambar (JPG / PNG / WEBP), apa pun jenisnya.
     * Jenis GAMBAR dipaparkan dahulu, diikuti jenis lain (Pelan, Geran, ...).
     * Setiap gambar ditukar kepada JPEG (maks. 1600px) supaya saiz PDF kecil.
     */
    private function gambarAset(string $jenis, Model $aset): array
    {
        $awalan = $jenis === 'bangunan' ? 'asbdoc' : 'asldoc';
        $gambar = [];

        $senarai = $aset->dokumen()
            ->orderBy("{$awalan}_id")
            ->get()
            ->sortBy(fn ($dokumen) => $dokumen->{"{$awalan}_type"} === 'GAMBAR' ? 0 : 1)
            ->values();

        $jumlahGambar = $senarai
            ->filter(fn ($dokumen) => $dokumen->{"{$awalan}_type"} === 'GAMBAR')
            ->count();
        $bilGambar = 0;

        foreach ($senarai as $dokumen) {
            $path = $dokumen->{"{$awalan}_path"};

            if (! in_array(strtolower(pathinfo((string) $path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $laluan = $this->laluanFail($path);

            $jpeg = $laluan ? $this->tukarKeJpeg($laluan) : null;

            if (! $jpeg) {
                continue;
            }

            $kodJenis = $dokumen->{"{$awalan}_type"};

            if ($kodJenis === 'GAMBAR') {
                $bilGambar++;
                $tajuk = 'Gambar Aset' . ($jumlahGambar > 1 ? " ({$bilGambar} / {$jumlahGambar})" : '');
            } else {
                $tajuk = $kodJenis ? Paparan::label($kodJenis) : 'Dokumen';
            }

            [$lebar, $tinggi] = getimagesize($jpeg);
            $nisbah = min(self::LEBAR_MAKS / $lebar, self::TINGGI_MAKS / $tinggi);

            $gambar[] = [
                'data' => base64_encode((string) file_get_contents($jpeg)),
                'lebar' => (int) round($lebar * $nisbah),
                'tinggi' => (int) round($tinggi * $nisbah),
                'tajuk' => $tajuk,
                'nama' => $dokumen->{"{$awalan}_name"},
                'tarikh' => $dokumen->uploaded_at,
            ];
        }

        return $gambar;
    }

    /**
     * Tukar gambar (JPG/PNG/WEBP) kepada JPEG berlatar putih, maksimum 1600px.
     */
    private function tukarKeJpeg(string $laluan): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $gambar = @imagecreatefromstring((string) file_get_contents($laluan));

        if (! $gambar) {
            return null;
        }

        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $skala = min(1, 1600 / max($lebar, $tinggi));
        $lebarBaru = max(1, (int) round($lebar * $skala));
        $tinggiBaru = max(1, (int) round($tinggi * $skala));

        $kanvas = imagecreatetruecolor($lebarBaru, $tinggiBaru);
        imagefill($kanvas, 0, 0, imagecolorallocate($kanvas, 255, 255, 255));
        imagecopyresampled($kanvas, $gambar, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);

        $sementara = tempnam(sys_get_temp_dir(), 'mpas-img-');
        imagejpeg($kanvas, $sementara, 80);

        imagedestroy($gambar);
        imagedestroy($kanvas);

        $this->failSementara[] = $sementara;

        return $sementara;
    }

    private function bersihkan(): void
    {
        foreach ($this->failSementara as $fail) {
            @unlink($fail);
        }

        $this->failSementara = [];
    }
}
