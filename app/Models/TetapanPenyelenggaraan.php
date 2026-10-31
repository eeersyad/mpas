<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TetapanPenyelenggaraan extends Model
{
    protected $table = 'tetapan_penyelenggaraan';

    protected $primaryKey = 'tpn_id';

    // Peranan yang boleh dipilih di Tetapan (SYSTEM sentiasa ada akses)
    public const PERANAN = ['BPH', 'BP', 'KJ'];

    protected $fillable = [
        'tpn_peranan_lihat',
        'tpn_peranan_isi',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tpn_peranan_lihat' => 'array',
            'tpn_peranan_isi' => 'array',
        ];
    }

    /**
     * Ambil satu-satunya rekod tetapan (cipta jika belum ada).
     */
    public static function semasa(): self
    {
        return static::query()->firstOrCreate([], [
            'tpn_peranan_lihat' => ['BPH'],
            'tpn_peranan_isi' => ['BPH'],
        ]);
    }

    public static function bolehLihat(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->usr_level === 'SYSTEM' || static::bolehIsi($user)) {
            return true;
        }

        return in_array($user->usr_level, static::semasa()->tpn_peranan_lihat ?? [], true);
    }

    public static function bolehIsi(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->usr_level === 'SYSTEM') {
            return true;
        }

        return in_array($user->usr_level, static::semasa()->tpn_peranan_isi ?? [], true);
    }
}