<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CamasServicio extends Model
{
    use HasFactory;

    protected $table = 'camas_servicios';

    protected $fillable = [
        'departamento_id',
        'nombre',
        'codigo',
        'piso',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function departamento()
    {
        return $this->belongsTo(
            CamasDepartamento::class,
            'departamento_id'
        );
    }

    public function habitaciones()
    {
        return $this->hasMany(
            CamasHabitacion::class,
            'servicio_id'
        );
    }

    public function camas()
    {
        return $this->hasMany(
            Cama::class,
            'servicio_id'
        );
    }
}
