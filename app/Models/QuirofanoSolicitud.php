<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuirofanoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'quirofano_solicitudes';

    protected $fillable = [
        'usuario_solicitante_id',
        'servicio_solicitante',

        'numero_cuenta',
        'id_paciente_sigh',
        'historia_clinica',
        'paciente',
        'dni',
        'sexo',
        'fecha_nacimiento',
        'edad',
        'tipo_seguro',
        'servicio_actual',
        'cama',
        'fecha_ingreso',

        'medico_codigo',
        'medico_nombre',
        'medico_especialidad',

        'diagnostico_codigo',
        'diagnostico_nombre',

        'procedimiento_codigo',
        'procedimiento_nombre',

        'tipo_solicitud',
        'prioridad',
        'duracion_estimada_minutos',
        'tipo_anestesia',
        'fecha_sugerida',
        'requerimientos_especiales',
        'observaciones',

        'estado',
        'motivo_observacion',
        'fecha_envio',
        'fecha_revision',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'datetime',
        'fecha_sugerida' => 'date',
        'fecha_envio' => 'datetime',
        'fecha_revision' => 'datetime',
    ];

    public function usuarioSolicitante()
    {
        return $this->belongsTo(
            User::class,
            'usuario_solicitante_id'
        );
    }

    public function scopeActivas($query)
    {
        return $query->whereNotIn('estado', [
            'CANCELADA',
            'RECHAZADA',
            'FINALIZADA',
        ]);
    }

    public function puedeEditar(): bool
    {
        return in_array(
            $this->estado,
            [
                'BORRADOR',
                'OBSERVADA',
            ],
            true
        );
    }

    public function puedeProgramarse(): bool
    {
        return $this->estado === 'APROBADA';
    }
}
