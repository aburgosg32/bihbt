<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CamasHabitacion extends Model
{
    use HasFactory;

    protected $table = 'camas_habitaciones';

    protected $fillable = [
        'servicio_id',
        'nombre',
        'numero',
        'piso',
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

    public function camas()
    {
        return $this->hasMany(
            Cama::class,
            'habitacion_id'
        );
    }
}
