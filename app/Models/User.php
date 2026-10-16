<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'usr_id';

    protected $fillable = [
        'usr_name',
        'usr_password',
        'usr_level',
        'usr_status',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'usr_password',
    ];

    protected function casts(): array
    {
        return [
            'usr_password' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->usr_password;
    }

    public function mempunyaiPeranan(string|array $peranan): bool
    {
        return in_array($this->usr_level, (array) $peranan, true);
    }

    public function asetDicipta(): HasMany
    {
        return $this->hasMany(Aset::class, 'created_by', 'usr_id');
    }
}