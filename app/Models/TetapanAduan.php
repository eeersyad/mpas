<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TetapanAduan extends Model
{
    protected $table = 'tetapan_aduan';

    protected $primaryKey = 'adu_id';

    protected $fillable = [
        'adu_tempoh_papar_selesai',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'adu_tempoh_papar_selesai' => 'integer',
        ];
    }
}