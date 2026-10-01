<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'procedimiento_programaciones',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('procedimiento_id')
                    ->constrained('procedimientos')
                    ->restrictOnDelete();

                $table->date('fecha');

                $table->time('hora_inicio');

                $table->time('hora_fin');

                $table->unsignedSmallInteger('intervalo_minutos')
                    ->default(15);

                $table->unsignedSmallInteger('capacidad_por_cupo')
                    ->default(1);

                /*
                 * Permite identificar las fechas creadas mediante
                 * una misma programación masiva.
                 */
                $table->uuid('codigo_grupo')
                    ->nullable();

                $table->boolean('activo')
                    ->default(true);

                $table->foreignId('usuario_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamps();

                /*
                 * Evita registrar exactamente dos veces la misma
                 * programación.
                 */
                $table->unique(
                    [
                        'procedimiento_id',
                        'fecha',
                        'hora_inicio',
                        'hora_fin',
                    ],
                    'programacion_fecha_horario_unique'
                );

                $table->index(
                    ['procedimiento_id', 'fecha', 'activo'],
                    'programacion_busqueda_index'
                );

                $table->index('codigo_grupo');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimiento_programaciones');
    }
};
