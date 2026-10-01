<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CamasDepartamento extends Model
{
    use HasFactory;

    protected $table = 'camas_departamentos';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function servicios()
    {
        return $this->hasMany(
            CamasServicio::class,
            'departamento_id'
        );
    }
}
