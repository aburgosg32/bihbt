<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuirofanoSala;
use App\Models\QuirofanoSolicitud;

class QuirofanoProgramacion extends Model
{
    use HasFactory;

    protected $table = 'quirofano_programaciones';

    protected $fillable = [
        'quirofano_solicitud_id',
        'quirofano_sala_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'fecha_hora_disponible',
        'duracion_operacion_minutos',
        'minutos_limpieza',
        'estado',
        'fecha_inicio_preparacion',
        'fecha_inicio_real',
        'fecha_fin_real',
        'fecha_fin_limpieza',
        'usuario_programador_id',
        'observaciones',
        'motivo_cancelacion',
        'fecha_cancelacion',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
        'fecha_hora_disponible' => 'datetime',
        'fecha_cancelacion' => 'datetime',
        'duracion_operacion_minutos' => 'integer',
        'minutos_limpieza' => 'integer',
        'fecha_inicio_preparacion' => 'datetime',
        'fecha_inicio_real' => 'datetime',
        'fecha_fin_real' => 'datetime',
        'fecha_fin_limpieza' => 'datetime',
    ];

    public const ESTADO_PROGRAMADA =
    'PROGRAMADA';

    public const ESTADO_PREPARACION =
    'EN_PREPARACION';

    public const ESTADO_CURSO =
    'EN_CURSO';

    public const ESTADO_FINALIZADA =
    'FINALIZADA';

    public const ESTADO_CANCELADA =
    'CANCELADA';

    public function solicitud()
    {
        return $this->belongsTo(
            QuirofanoSolicitud::class,
            'quirofano_solicitud_id'
        );
    }

    public function sala()
    {
        return $this->belongsTo(
            QuirofanoSala::class,
            'quirofano_sala_id'
        );
    }

    public function usuarioProgramador()
    {
        return $this->belongsTo(
            User::class,
            'usuario_programador_id'
        );
    }
}
