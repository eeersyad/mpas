<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bangunan extends Model
{
    use HasFactory;

    protected $table = 'assets_building';

    protected $primaryKey = 'asb_id';

    protected $fillable = [
        'ast_id',
        'asb_ref_no',
        'asb_name',
        'asb_no_file',
        'asb_add1',
        'asb_add2',
        'asb_postcode',
        'asb_distinct',
        'asb_mukim',
        'asb_city',
        'asb_category',
        'asb_sub_category',
        'asb_size',
        'asb_agency',
        'asb_ministry',
        'asb_dept',
        'asb_state',
        'asb_country',
        'asb_gmaps_location',
        'asb_latitude',
        'asb_longitude',
        'asb_aset_warisan',
        'asb_kos_siap_bina_asal',
        'asb_kos_tambahan',
        'asb_total_kos',
        'asb_completion_date',
        'asb_financing_source',
        'asb_ptj_code',
        'asb_current_value',
        'asb_assessment_date',
        'asb_total_block',
        'asb_total_ext_structure',
        'asb_tnb_acc_no',
        'asb_satu_acc_no',
        'asb_maintenance_status',
        'asb_premise_status',
        'asb_collector_name',
        'asb_collector_position',
        'asb_collected_at',
        'asb_verifier_name',
        'asb_verifier_position',
        'asb_verified_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'asb_aset_warisan' => 'boolean',
            'asb_size' => 'decimal:2',
            'asb_kos_siap_bina_asal' => 'decimal:2',
            'asb_kos_tambahan' => 'decimal:2',
            'asb_total_kos' => 'decimal:2',
            'asb_current_value' => 'decimal:2',
            'asb_completion_date' => 'date',
            'asb_assessment_date' => 'date',
            'asb_collected_at' => 'datetime',
            'asb_verified_at' => 'datetime',
        ];
    }

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class, 'ast_id', 'ast_id');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenBangunan::class, 'asb_id', 'asb_id');
    }

    public function penyewa(): HasMany
    {
        return $this->hasMany(Penyewa::class, 'asb_id', 'asb_id');
    }

    public function kod(): HasOne
    {
        return $this->hasOne(KodBangunan::class, 'asb_id', 'asb_id');
    }

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class, 'asb_id', 'asb_id');
    }

    
    public function penyelenggaraan(): HasMany
    {
        return $this->hasMany(Penyelenggaraan::class, 'asb_id', 'asb_id')
            ->orderByDesc('asm_inspection_date')
            ->orderByDesc('asm_id');
    }

    public function penyelenggaraanTerkini(): HasOne
    {
        return $this->hasOne(Penyelenggaraan::class, 'asb_id', 'asb_id')
            ->ofMany([
                'asm_inspection_date' => 'max',
                'asm_id' => 'max',
            ]);
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