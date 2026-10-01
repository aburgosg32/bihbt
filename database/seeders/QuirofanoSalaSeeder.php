<?php

namespace Database\Seeders;

use App\Models\QuirofanoSala;
use Illuminate\Database\Seeder;

class QuirofanoSalaSeeder extends Seeder
{
    public function run(): void
    {
        for ($numero = 1; $numero <= 8; $numero++) {
            QuirofanoSala::updateOrCreate(
                [
                    'codigo' => 'SALA-' . str_pad(
                        (string) $numero,
                        2,
                        '0',
                        STR_PAD_LEFT
                    ),
                ],
                [
                    'nombre' => 'Sala de Operaciones ' . $numero,
                    'ubicacion' => 'Centro Quirúrgico',
                    'estado_actual' => 'DISPONIBLE',
                    'operativa' => true,
                ]
            );
        }
    }
}
