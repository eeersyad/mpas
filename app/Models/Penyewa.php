<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'asb_tenant';

    protected $primaryKey = 'tnt_id';

    protected $fillable = [
        'asb_id',
        'tnt_name',
        'tnt_acc_no',
        'tnt_ic_no',
        'tnt_mula_perjanjian',
        'tnt_tamat_perjanjian',
        'tnt_status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tnt_mula_perjanjian' => 'date',
            'tnt_tamat_perjanjian' => 'date',
        ];
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class, 'asb_id', 'asb_id');
    }

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class, 'tnt_id', 'tnt_id');
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