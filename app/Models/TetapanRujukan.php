<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TetapanRujukan extends Model
{
    use HasFactory;

    protected $table = 'tetapan_rujukan';

    protected $primaryKey = 'ruj_id';

    protected $fillable = [
        'ruj_jenis',
        'ruj_awalan',
        'ruj_nombor_mula',
        'ruj_nombor_semasa',
        'ruj_panjang_nombor',
        'created_by',
        'updated_by',
    ];
}