<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcedimientoServicio extends Model
{
    use HasFactory;

    protected $table = 'procedimiento_servicios';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function procedimientos(): HasMany
    {
        return $this->hasMany(
            Procedimiento::class,
            'procedimiento_servicio_id'
        );
    }
}