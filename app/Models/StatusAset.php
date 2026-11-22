<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Status premis (BANGUNAN) dan status tanah (TANAH) yang diurus di Tetapan.
 * Kod dijana sekali dan dikunci; label, warna dan suis boleh diubah.
 */
class StatusAset extends Model
{
    protected $table = 'tetapan_status_aset';

    protected $primaryKey = 'sts_id';

    public const JENIS = [
        'BANGUNAN' => 'Status Premis',
        'TANAH' => 'Status Tanah',
    ];

    // Warna badge yang boleh dipilih (kelas CSS: lencana-jadual status-{warna})
    public const WARNA = [
        'hijau' => 'Hijau',
        'merah' => 'Merah',
        'ungu' => 'Ungu',
        'kuning' => 'Kuning',
        'biru' => 'Biru',
        'jingga' => 'Jingga',
        'teal' => 'Teal',
        'kelabu' => 'Kelabu',
    ];

    // Warna carta dashboard (padan dengan warna badge)
    public const WARNA_HEX = [
        'hijau' => '#6FC7BD',
        'merah' => '#EFA1B3',
        'ungu' => '#B9A3F5',
        'kuning' => '#F6D58B',
        'biru' => '#8FB7F7',
        'jingga' => '#F7B98A',
        'teal' => '#7FD3E0',
        'kelabu' => '#B8C2C1',
    ];

    protected $fillable = [
        'sts_jenis',
        'sts_kod',
        'sts_label',
        'sts_warna',
        'sts_boleh_iklan',
        'sts_susunan',
        'sts_status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sts_boleh_iklan' => 'boolean',
            'sts_susunan' => 'integer',
        ];
    }

    /** Cache ringkas bagi setiap permintaan */
    private static array $cache = [];

    public function aktif(): bool
    {
        return $this->sts_status === 'ACTIVE';
    }

    /** Warna pilihan sendiri disimpan sebagai kod hex, cth. #E91E63 */
    public static function adalahHex(?string $warna): bool
    {
        return (bool) preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $warna);
    }

    public function warnaSuai(): bool
    {
        return self::adalahHex($this->sts_warna);
    }

    public function kelasWarna(): string
    {
        if ($this->warnaSuai()) {
            return 'status-suai';
        }

        return 'status-' . (array_key_exists($this->sts_warna, self::WARNA) ? $this->sts_warna : 'kelabu');
    }

    /**
     * Gaya sebaris untuk warna pilihan sendiri: latar lut sinar & teks versi gelap (mudah dibaca).
     */
    public function gayaWarna(): string
    {
        if (! $this->warnaSuai()) {
            return '';
        }

        return '--warna-status: ' . $this->sts_warna . '; --warna-status-teks: ' . self::gelapkan($this->sts_warna, 0.45) . ';';
    }

    /** Kod hex untuk carta dashboard */
    public function hex(): string
    {
        return $this->warnaSuai()
            ? strtoupper($this->sts_warna)
            : (self::WARNA_HEX[$this->sts_warna] ?? self::WARNA_HEX['kelabu']);
    }

    private static function gelapkan(string $hex, float $kadar): string
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return sprintf('#%02X%02X%02X', (int) round($r * (1 - $kadar)), (int) round($g * (1 - $kadar)), (int) round($b * (1 - $kadar)));
    }

    public function scopeJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('sts_jenis', $jenis);
    }

    public function scopeTersusun(Builder $query): Builder
    {
        return $query->orderBy('sts_susunan')->orderBy('sts_label');
    }

    /**
     * Semua status bagi satu jenis (termasuk tidak aktif), dikunci ikut kod.
     */
    public static function semua(string $jenis): Collection
    {
        return static::$cache[$jenis] ??= static::query()->jenis($jenis)->tersusun()->get()->keyBy('sts_kod');
    }

    /**
     * Pilihan untuk borang / penapis: status aktif, serta status semasa rekod walaupun sudah tidak aktif.
     */
    public static function pilihan(string $jenis, ?string $kodSemasa = null): Collection
    {
        return static::semua($jenis)->filter(fn (self $s) => $s->aktif() || $s->sts_kod === $kodSemasa);
    }

    public static function cari(string $jenis, ?string $kod): ?self
    {
        return $kod ? static::semua($jenis)->get($kod) : null;
    }

    public static function label(string $jenis, ?string $kod): string
    {
        if (! $kod) {
            return '-';
        }

        return static::cari($jenis, $kod)?->sts_label ?? \App\Support\Paparan::label($kod);
    }

    public static function kelas(string $jenis, ?string $kod): string
    {
        return static::cari($jenis, $kod)?->kelasWarna() ?? 'kelabu';
    }

    /**
     * Kod yang sah untuk disimpan: status aktif, serta kod semasa rekod (supaya rekod lama boleh dikemas kini).
     */
    public static function kodSah(string $jenis, ?string $kodSemasa = null): array
    {
        return static::pilihan($jenis, $kodSemasa)->keys()->all();
    }

    /** Kod status yang boleh diiklankan (bangunan) */
    public static function kodBolehIklan(): array
    {
        return static::semua('BANGUNAN')->filter(fn (self $s) => $s->sts_boleh_iklan)->keys()->all();
    }

    /**
     * Data carta dashboard: semua status aktif (serta status tidak aktif yang masih digunakan),
     * mengikut susunan di Tetapan. $jumlah = [kod => bilangan].
     */
    public static function dataCarta(string $jenis, iterable $jumlah): array
    {
        $jumlah = collect($jumlah);

        $status = static::semua($jenis)->filter(
            fn (self $s) => $s->aktif() || (int) ($jumlah[$s->sts_kod] ?? 0) > 0
        );

        return [
            'label' => $status->pluck('sts_label')->values()->all(),
            'nilai' => $status->map(fn (self $s) => (int) ($jumlah[$s->sts_kod] ?? 0))->values()->all(),
            'kod' => $status->keys()->values()->all(),
            'warna' => $status->map(fn (self $s) => $s->hex())->values()->all(),
        ];
    }

    public static function lupakanCache(): void
    {
        static::$cache = [];
    }
}
