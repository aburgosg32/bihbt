<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProcedimientoCupo extends Model
{
    use HasFactory;

    public const ESTADO_DISPONIBLE = 'DISPONIBLE';
    public const ESTADO_RESERVADO = 'RESERVADO';
    public const ESTADO_BLOQUEADO = 'BLOQUEADO';

    protected $table = 'procedimiento_cupos';

    protected $fillable = [
        'procedimiento_programacion_id',
        'hora',
        'estado',
        'motivo_bloqueo',
    ];

    protected $casts = [
        'procedimiento_programacion_id' => 'integer',
    ];

    public function programacion(): BelongsTo
    {
        return $this->belongsTo(
            ProcedimientoProgramacion::class,
            'procedimiento_programacion_id'
        );
    }
    public function cita(): HasOne
    {
        return $this->hasOne(
            ProcedimientoCita::class,
            'procedimiento_cupo_id'
        );
    }
}
