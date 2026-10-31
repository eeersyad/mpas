<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomponenPenyelenggaraan extends Model
{
    protected $table = 'asb_maintenance_component';

    protected $primaryKey = 'amc_id';

    protected $fillable = [
        'amc_name',
        'amc_items',
        'amc_order',
        'amc_status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'amc_items' => 'array',
            'amc_order' => 'integer',
        ];
    }

    /**
     * Hanya komponen aktif, ikut turutan.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('amc_status', 'ACTIVE')
            ->orderBy('amc_order')
            ->orderBy('amc_id');
    }

    /**
     * Item yang aktif sahaja (untuk borang pemeriksaan baru).
     */
    public function itemAktif(): array
    {
        return collect($this->amc_items ?? [])
            ->where('status', 'ACTIVE')
            ->values()
            ->all();
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