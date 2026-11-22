<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AksesPenyelenggaraan extends Model
{
    protected $table = 'tetapan_akses_penyelenggaraan';

    protected $primaryKey = 'tap_id';

    public const SENARAI_TAHAP = [
        'LIHAT' => 'Lihat sahaja',
        'ISI' => 'Lihat & Isi',
    ];

    protected $fillable = [
        'usr_id',
        'tap_tahap',
        'tap_status',
        'created_by',
        'updated_by',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id', 'usr_id');
    }

    public function aktif(): bool
    {
        return $this->tap_status === 'ACTIVE';
    }

    public function bolehIsi(): bool
    {
        return $this->tap_tahap === 'ISI';
    }

    public function labelTahap(): string
    {
        return self::SENARAI_TAHAP[$this->tap_tahap] ?? $this->tap_tahap;
    }
}
