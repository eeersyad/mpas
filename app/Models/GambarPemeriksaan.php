<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GambarPemeriksaan extends Model
{
    protected $table = 'asb_maintenance_image';

    protected $primaryKey = 'asmi_id';

    protected $fillable = [
        'asm_id',
        'asmi_name',
        'asmi_summary',
        'asmi_path',
        'asmi_mime',
        'asmi_size',
        'uploaded_by',
    ];

    public function getRouteKeyName(): string
    {
        return 'asmi_id';
    }

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Penyelenggaraan::class, 'asm_id', 'asm_id');
    }

    public function dimuatNaikOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'usr_id');
    }

    /**
     * Nama fail semasa muat turun, contoh: kebocoran-siling-bilik-2.jpg
     */
    public function namaFailMuatTurun(): string
    {
        $sambungan = pathinfo($this->asmi_path, PATHINFO_EXTENSION) ?: 'jpg';

        return (Str::slug($this->asmi_name) ?: 'gambar-pemeriksaan') . '.' . $sambungan;
    }
}
