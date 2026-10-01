<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcedimientoProgramacion extends Model
{
    use HasFactory;

    protected $table = 'procedimiento_programaciones';

    protected $fillable = [
        'procedimiento_id',
        'procedimiento_medico_id',
        'fecha_programacion',
        'hora_inicio',
        'hora_fin',
        'intervalo_minutos',
        'cantidad_maxima',
        'activo',
    ];

    protected $casts = [
        'procedimiento_id' => 'integer',
        'procedimiento_medico_id' => 'integer',
        'fecha_programacion' => 'date',
        'intervalo_minutos' => 'integer',
        'cantidad_maxima' => 'integer',
        'activo' => 'boolean',
    ];

    public function procedimiento(): BelongsTo
    {
        return $this->belongsTo(
            Procedimiento::class,
            'procedimiento_id'
        );
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(
            ProcedimientoMedico::class,
            'procedimiento_medico_id'
        );
    }
    public function cupos(): HasMany
    {
        return $this->hasMany(
            ProcedimientoCupo::class,
            'procedimiento_programacion_id'
        )->orderBy('hora');
    }
}
