<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Aduan extends Model
{
    use HasFactory;

    protected $table = 'complaint';

    protected $primaryKey = 'cmp_id';

    public function getRouteKeyName(): string
    {
        return 'cmp_id';
    }

    protected $fillable = [
        'asb_id',
        'tnt_id',
        'cmp_reporter_name',
        'cmp_reporter_phone',
        'cmp_reporter_email',
        'cmp_ref_no',
        'cmp_received_date',
        'cmp_note',
        'cmp_damage_type',
        'cmp_status',
        'cmp_accepted_by',
        'cmp_accepted_at',
        'cmp_maintenance_date',
        'cmp_maintained_by',
        'cmp_completed_at',
        'cmp_completion_note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'cmp_received_date' => 'datetime',
            'cmp_accepted_at' => 'datetime',
            'cmp_maintenance_date' => 'date',
            'cmp_completed_at' => 'datetime',
        ];
    }

    /**
     * Jana no. rujukan, contoh ADU-2026-00042.
     */
    public static function binaNoRujukan(self $aduan): string
    {
        return sprintf(
            'ADU-%s-%05d',
            ($aduan->cmp_received_date ?? now())->format('Y'),
            $aduan->cmp_id
        );
    }

    /**
     * Pautan bertandatangan ke halaman status awam (untuk emel).
     */
    public function urlStatus(): string
    {
        return URL::signedRoute('aduan.status.papar', ['rujukan' => $this->cmp_ref_no]);
    }

    /**
     * Aduan yang ditolak BPH disimpan sebagai SELESAI dengan catatan khas.
     */
    public function ditolak(): bool
    {
        return $this->cmp_status === 'SELESAI'
            && Str::startsWith((string) $this->cmp_completion_note, 'Aduan ditolak oleh BPH:');
    }

    public function sebabDitolak(): string
    {
        return trim(Str::after((string) $this->cmp_completion_note, 'Aduan ditolak oleh BPH:'));
    }

    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class, 'asb_id', 'asb_id');
    }

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'tnt_id', 'tnt_id');
    }

    public function diterimaOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cmp_accepted_by', 'usr_id');
    }

    public function disenggaraOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cmp_maintained_by', 'usr_id');
    }

    public function gambar(): HasMany
    {
        return $this->hasMany(GambarAduan::class, 'cmp_id', 'cmp_id');
    }

    public function gambarSiap(): HasMany
    {
        return $this->hasMany(
            GambarSiapAduan::class,
            'cmp_id',
            'cmp_id'
        )->orderBy('cmpcimg_order');
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