<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'dni',
        'name',
        'email',
        'password',
        'rol',
        'activo',
        'cambiar_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'cambiar_password' => 'boolean',
        ];
    }

    public function permisosModulos(): HasMany
    {
        return $this->hasMany(UsuarioModuloPermiso::class, 'user_id');
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function puedeVerModulo(string $modulo): bool
    {
        if (! $this->activo) {
            return false;
        }

        if ($this->esAdministrador()) {
            return true;
        }

        return $this->permisosModulos()
            ->where('modulo', $modulo)
            ->where(function ($query) {
                $query->where('ver', true)
                    ->orWhere('gestionar', true);
            })
            ->exists();
    }

    public function puedeGestionarModulo(string $modulo): bool
    {
        if (! $this->activo) {
            return false;
        }

        if ($this->esAdministrador()) {
            return true;
        }

        return $this->permisosModulos()
            ->where('modulo', $modulo)
            ->where('gestionar', true)
            ->exists();
    }
}
