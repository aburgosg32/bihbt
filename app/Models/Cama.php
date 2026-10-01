<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cama extends Model
{
    use HasFactory;

    protected $table = 'camas';

    protected $fillable = [
        'servicio_id',
        'habitacion_id',
        'codigo',
        'numero',
        'tipo_cama',
        'estado_operativo',
        'sexo_permitido',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function servicio()
    {
        return $this->belongsTo(
            CamasServicio::class,
            'servicio_id'
        );
    }

    public function habitacion()
    {
        return $this->belongsTo(
            CamasHabitacion::class,
            'habitacion_id'
        );
    }
    public function ocupaciones()
    {
        return $this->hasMany(
            CamaOcupacion::class,
            'cama_id'
        );
    }

    public function ocupacionActiva()
    {
        return $this->hasOne(
            CamaOcupacion::class,
            'cama_id'
        )
            ->where('estado', 'ACTIVO')
            ->latestOfMany();
    }
}
