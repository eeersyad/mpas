<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aduan extends Model
{
    use HasFactory;

    protected $table = 'complaint';

    protected $primaryKey = 'cmp_id';

    protected $fillable = [
        'asb_id',
        'tnt_id',
        'cmp_received_date',
        'cmp_note',
        'cmp_damage_type',
        'cmp_status',
        'cmp_accepted_by',
        'cmp_accepted_at',
        'cmp_maintenance_date',
        'cmp_maintained_by',
        'cmp_completed_at',
        'cmp_completion_note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'cmp_received_date' => 'datetime',
            'cmp_accepted_at' => 'datetime',
            'cmp_maintenance_date' => 'date',
            'cmp_completed_at' => 'datetime',
        ];
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class, 'asb_id', 'asb_id');
    }

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'tnt_id', 'tnt_id');
    }

    public function diterimaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cmp_accepted_by', 'usr_id');
    }

    public function disenggaraOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cmp_maintained_by', 'usr_id');
    }

    public function gambar(): HasMany
    {
        return $this->hasMany(GambarAduan::class, 'cmp_id', 'cmp_id');
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