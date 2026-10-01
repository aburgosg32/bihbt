<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Procedimiento extends Model
{
    use HasFactory;

    protected $table = 'procedimientos';

    protected $fillable = [
        'procedimiento_servicio_id',
        'codigo',
        'nombre',
        'descripcion',
        'duracion_minutos',
        'color',
        'indicaciones',
        'activo',
    ];

    protected $casts = [
        'procedimiento_servicio_id' => 'integer',
        'duracion_minutos' => 'integer',
        'activo' => 'boolean',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            ProcedimientoServicio::class,
            'procedimiento_servicio_id'
        );
    }
}
