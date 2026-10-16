<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GambarAduan extends Model
{
    use HasFactory;

    protected $table = 'complaint_image';

    protected $primaryKey = 'cmpimg_id';

    public $timestamps = false;

    protected $fillable = [
        'cmp_id',
        'cmpimg_position',
        'cmpimg_name',
        'cmpimg_path',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function aduan(): BelongsTo
    {
        return $this->belongsTo(Aduan::class, 'cmp_id', 'cmp_id');
    }
}