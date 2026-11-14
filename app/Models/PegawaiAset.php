<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PegawaiAset extends Model
{
    protected $table = 'tetapan_pegawai';

    protected $primaryKey = 'pgw_id';

    public const SENARAI_PERANAN = ['PENGUMPUL', 'PENGESAH', 'KEDUA_DUA'];

    protected $fillable = [
        'pgw_nama',
        'pgw_jawatan',
        'pgw_peranan',
        'pgw_status',
        'created_by',
        'updated_by',
    ];

    public function scopePengumpul(Builder $query): Builder
    {
        return $query->whereIn('pgw_peranan', ['PENGUMPUL', 'KEDUA_DUA']);
    }

    public function scopePengesah(Builder $query): Builder
    {
        return $query->whereIn('pgw_peranan', ['PENGESAH', 'KEDUA_DUA']);
    }

    /**
     * Huruf awal nama untuk avatar (abaikan bin/binti).
     */
    public function inisial(): string
    {
        return collect(preg_split('/\s+/', trim((string) $this->pgw_nama)))
            ->reject(fn ($kata) => in_array(mb_strtolower($kata), ['bin', 'binti', 'bt', 'bte', 'b.', 'a/l', 'a/p'], true))
            ->filter()
            ->take(2)
            ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
            ->implode('');
    }

    public function aktif(): bool
    {
        return $this->pgw_status === 'ACTIVE';
    }
}