<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyelenggaraan extends Model
{
    protected $table = 'asb_maintenance';

    protected $primaryKey = 'asm_id';

    public const NILAI = [
        'BAIK',
        'ROSAK',
        'TIDAK_BERKAITAN',
    ];

    protected $fillable = [
        'asb_id',
        'asm_inspection_date',
        'asm_inspected_by',
        'asm_items',
        'asm_note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'asm_inspection_date' => 'date',
            'asm_items' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'asm_id';
    }

    /**
     * Dapatkan nilai satu item, contoh: $rekod->nilaiItem(1, 'k1i2') → 'ROSAK'
     */
    public function nilaiItem(int $amcId, string $itemId): ?string
    {
        $komponen = collect($this->asm_items ?? [])
            ->firstWhere('amc_id', $amcId);

        if (! $komponen) {
            return null;
        }

        return collect($komponen['item'] ?? [])
            ->firstWhere('id', $itemId)['nilai'] ?? null;
    }

    /**
     * Bilangan item mengikut nilai, contoh: ['BAIK' => 10, 'ROSAK' => 2, 'TIDAK_BERKAITAN' => 1]
     */
    public function ringkasanNilai(): array
    {
        $kiraan = array_fill_keys(self::NILAI, 0);

        foreach ($this->asm_items ?? [] as $komponen) {
            foreach ($komponen['item'] ?? [] as $item) {
                if (isset($kiraan[$item['nilai'] ?? null])) {
                    $kiraan[$item['nilai']]++;
                }
            }
        }

        return $kiraan;
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class, 'asb_id', 'asb_id');
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asm_inspected_by', 'usr_id');
    }

    public function diciptaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'usr_id');
    }

    public function dikemaskiniOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'usr_id');
    }
}