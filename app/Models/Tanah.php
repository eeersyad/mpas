<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tanah extends Model
{
    use HasFactory;

    protected $table = 'assets_land';

    protected $primaryKey = 'asl_id';

    public $timestamps = false;

    protected $fillable = [
        'ast_id',
        'asl_ref_no',
        'asl_lot_no',
        'asl_location',
        'asl_gmaps_location',
        'asl_latitude',
        'asl_longitude',
        'asl_tarikh_terima_hakmilik',
        'asl_tarikh_serah_balik_hakmilik',
        'asl_status_hakmilik',
        'asl_land_size',
        'asl_hakmilik_no',
        'asl_hakmilik_type',
        'asl_land_price',
        'asl_land_skp',
        'asl_cukaiT',
        'asl_status_cukaiT',
    ];

    protected function casts(): array
    {
        return [
            'asl_tarikh_terima_hakmilik' => 'date',
            'asl_tarikh_serah_balik_hakmilik' => 'date',
            'asl_land_size' => 'decimal:2',
            'asl_land_price' => 'decimal:2',
            'asl_land_skp' => 'decimal:2',
            'asl_cukaiT' => 'decimal:2',
        ];
    }

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class, 'ast_id', 'ast_id');
    }
}