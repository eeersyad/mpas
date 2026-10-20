<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenTanah extends Model
{
    use HasFactory;

    protected $table = 'asl_document';

    protected $primaryKey = 'asldoc_id';

    public $timestamps = false;

    protected $fillable = [
        'asl_id',
        'asldoc_type',
        'asldoc_name',
        'asldoc_path',
        'uploaded_by',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function tanah(): BelongsTo
    {
        return $this->belongsTo(Tanah::class, 'asl_id', 'asl_id');
    }

    public function dimuatNaikOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'usr_id');
    }
}