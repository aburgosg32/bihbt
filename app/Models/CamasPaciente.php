<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamasPaciente extends Model
{
    protected $table = 'camas_pacientes';

    protected $fillable = [
        'id_paciente_sigh',
        'nro_historia_clinica',
        'nombre_completo',
        'telefono',
        'sexo',
        'fecha_nacimiento',
    ];
    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function ocupaciones()
    {
        return $this->hasMany(
            CamaOcupacion::class,
            'paciente_id'
        );
    }

    public function ocupacionActiva()
    {
        return $this->hasOne(
            CamaOcupacion::class,
            'paciente_id'
        )->where('estado', 'ACTIVO');
    }
}
