<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcedimientoMedico extends Model
{
    use HasFactory;

    protected $table = 'procedimiento_medicos';

    protected $fillable = [
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'especialidad',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim(
            $this->apellido_paterno . ' ' .
                ($this->apellido_materno ?? '') . ' ' .
                $this->nombres
        );
    }
    public function programaciones(): HasMany
    {
        return $this->hasMany(
            ProcedimientoProgramacion::class,
            'procedimiento_medico_id'
        );
    }
}
