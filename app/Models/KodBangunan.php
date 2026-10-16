<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KodBangunan extends Model
{
    use HasFactory;

    protected $table = 'assets_building_code';

    protected $primaryKey = 'qr_id';

    public $timestamps = false;

    protected $fillable = [
        'asb_id',
        'qr_code_value',
        'qr_image_path',
        'qr_status',
        'generated_at',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class, 'asb_id', 'asb_id');
    }

    public function dijanaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by', 'usr_id');
    }
}