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

    public function aktif(): bool
    {
        return $this->pgw_status === 'ACTIVE';
    }
}