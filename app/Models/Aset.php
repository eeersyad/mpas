<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    protected $primaryKey = 'ast_id';

    protected $fillable = [
        'ast_name',
        'created_by',
        'updated_by',
    ];

    public function bangunan(): HasOne
    {
        return $this->hasOne(Bangunan::class, 'ast_id', 'ast_id');
    }

    public function tanah(): HasOne
    {
        return $this->hasOne(Tanah::class, 'ast_id', 'ast_id');
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