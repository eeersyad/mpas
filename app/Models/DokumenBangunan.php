<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenBangunan extends Model
{
    use HasFactory;

    protected $table = 'asb_document';

    protected $primaryKey = 'asbdoc_id';

    public $timestamps = false;

    protected $fillable = [
        'asb_id',
        'asbdoc_type',
        'asbdoc_name',
        'asbdoc_path',
        'uploaded_by',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class, 'asb_id', 'asb_id');
    }

    public function dimuatNaikOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'usr_id');
    }
}