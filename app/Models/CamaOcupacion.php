<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamaOcupacion extends Model
{
    protected $table = 'cama_ocupaciones';

    protected $fillable = [
        'paciente_id',
        'cama_id',
        'fecha_hora_ingreso',
        'fecha_hora_alta',
        'estado',
        'usuario_ingreso_id',
        'usuario_alta_id',
    ];

    protected $casts = [
        'fecha_hora_ingreso' => 'datetime',
        'fecha_hora_alta' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(
            CamasPaciente::class,
            'paciente_id'
        );
    }

    public function cama()
    {
        return $this->belongsTo(
            Cama::class,
            'cama_id'
        );
    }
}
