<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcedimientoCita extends Model
{
    use HasFactory;

    public const ESTADO_PROGRAMADA = 'PROGRAMADA';
    public const ESTADO_ATENDIDA = 'ATENDIDA';
    public const ESTADO_ANULADA = 'ANULADA';

    protected $table = 'procedimiento_citas';

    protected $fillable = [
        'procedimiento_cupo_id',
        'numero_cuenta',
        'id_paciente_sigh',
        'historia_clinica',
        'paciente',
        'telefono',
        'sexo',
        'fecha_nacimiento',
        'servicio_origen',
        'tipo_servicio',
        'financiamiento',
        'estado',
        'observacion',
        'fecha_registro',
    ];

    protected $casts = [
        'procedimiento_cupo_id' => 'integer',
        'fecha_nacimiento' => 'date',
        'fecha_registro' => 'datetime',
    ];

    public function cupo(): BelongsTo
    {
        return $this->belongsTo(
            ProcedimientoCupo::class,
            'procedimiento_cupo_id'
        );
    }
}
