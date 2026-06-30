<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologiaCita extends Model
{
    protected $table = 'radiologia_citas';

    protected $fillable = [
        'id_cuenta_atencion',
        'paciente',
        'nro_historia_clinica',
        'telefono',
        'fecha_nacimiento',
        'edad',
        'tipo_edad',
        'fecha_ingreso',
        'hora_ingreso',
        'tipo_servicio',
        'nombre_servicio',
        'fuente_financiamiento',
        'modalidad',
        'codigo_estudio',
        'estudio',
        'fecha_cita',
        'hora_cita',
        'estado',
        'observacion',
        'usuario_registra_id',
    ];
}
