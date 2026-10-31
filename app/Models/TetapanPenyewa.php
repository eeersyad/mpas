<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TetapanPenyewa extends Model
{
    protected $table = 'tetapan_penyewa';

    protected $primaryKey = 'tpy_id';

    protected $fillable = [
        'tpy_tempoh_hampir_tamat_perjanjian',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tpy_tempoh_hampir_tamat_perjanjian' => 'integer',
        ];
    }
}
