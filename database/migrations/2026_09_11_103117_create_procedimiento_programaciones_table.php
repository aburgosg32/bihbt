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

                $table->foreignId('procedimiento_medico_id')
                    ->constrained('procedimiento_medicos')
                    ->restrictOnDelete();

                $table->date('fecha_programacion');

                $table->time('hora_inicio');

                $table->time('hora_fin');

                $table->unsignedSmallInteger('intervalo_minutos')
                    ->default(15);

                $table->unsignedSmallInteger('cantidad_maxima')
                    ->nullable();

                $table->boolean('activo')
                    ->default(true);

                $table->timestamps();

                $table->index(
                    [
                        'fecha_programacion',
                        'procedimiento_id',
                        'activo',
                    ],
                    'programaciones_fecha_procedimiento_index'
                );

                $table->index(
                    [
                        'fecha_programacion',
                        'procedimiento_medico_id',
                        'activo',
                    ],
                    'programaciones_fecha_medico_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimiento_programaciones');
    }
};
