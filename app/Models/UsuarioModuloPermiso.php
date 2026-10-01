<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioModuloPermiso extends Model
{
    protected $table = 'usuario_modulo_permisos';

    protected $fillable = [
        'user_id',
        'modulo',
        'ver',
        'gestionar',
    ];

    protected function casts(): array
    {
        return [
            'ver' => 'boolean',
            'gestionar' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
