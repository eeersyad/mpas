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

    /**
     * Akses ikut pengguna (Tetapan > Akses Penyelenggaraan). SYSTEM sentiasa ada akses penuh.
     * Disimpan sementara bagi setiap permintaan supaya tidak query berulang kali.
     */
    private static array $aksesPengguna = [];

    private static function akses(User $user): ?AksesPenyelenggaraan
    {
        if (! array_key_exists($user->usr_id, static::$aksesPengguna)) {
            static::$aksesPengguna[$user->usr_id] = AksesPenyelenggaraan::query()
                ->where('usr_id', $user->usr_id)
                ->where('tap_status', 'ACTIVE')
                ->first();
        }

        return static::$aksesPengguna[$user->usr_id];
    }

    public static function bolehLihat(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->usr_level === 'SYSTEM' || static::akses($user) !== null;
    }

    public static function bolehIsi(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->usr_level === 'SYSTEM' || (bool) static::akses($user)?->bolehIsi();
    }
}
