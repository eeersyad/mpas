<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GambarSiapAduan extends Model
{
    use HasFactory;

    protected $table = 'complaint_completion_image';

    protected $primaryKey = 'cmpcimg_id';

    protected $fillable = [
        'cmp_id',
        'cmpcimg_name',
        'cmpcimg_path',
        'cmpcimg_order',
        'created_by',
    ];

    public function aduan(): BelongsTo
    {
        return $this->belongsTo(Aduan::class, 'cmp_id', 'cmp_id');
    }

    public function dimuatNaikOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'usr_id');
    }
}