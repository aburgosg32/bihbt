<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuirofanoSala extends Model
{
    use HasFactory;

    protected $table = 'quirofano_salas';

    protected $fillable = [
        'nombre',
        'codigo',
        'ubicacion',
        'tipo_sala',
        'hora_inicio_jornada',
        'hora_fin_jornada',
        'minutos_limpieza',
        'estado_actual',
        'operativa',
        'observaciones',
    ];

    protected $casts = [
        'operativa' => 'boolean',
        'minutos_limpieza' => 'integer',
    ];

    public const TIPO_PROGRAMADA = 'PROGRAMADA';

    public const TIPO_EMERGENCIA = 'EMERGENCIA';

    public const TIPOS_SALA = [
        self::TIPO_PROGRAMADA,
        self::TIPO_EMERGENCIA,
    ];

    public const ESTADOS = [
        'DISPONIBLE',
        'PROGRAMADA',
        'EN_PREPARACION',
        'EN_USO',
        'EN_LIMPIEZA',
        'MANTENIMIENTO',
    ];

    public function estaDisponible(): bool
    {
        return $this->operativa
            && $this->estado_actual === 'DISPONIBLE';
    }

    public function esProgramada(): bool
    {
        return $this->tipo_sala === self::TIPO_PROGRAMADA;
    }

    public function esEmergencia(): bool
    {
        return $this->tipo_sala === self::TIPO_EMERGENCIA;
    }

    public function funcionaTodoElDia(): bool
    {
        return $this->esEmergencia();
    }
}
