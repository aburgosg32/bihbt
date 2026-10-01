<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimiento_cupos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procedimiento_programacion_id')
                ->constrained('procedimiento_programaciones')
                ->cascadeOnDelete();

            $table->time('hora');

            $table->string('estado', 20)
                ->default('DISPONIBLE');

            $table->text('motivo_bloqueo')
                ->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'procedimiento_programacion_id',
                    'hora',
                ],
                'procedimiento_cupos_programacion_hora_unique'
            );

            $table->index(
                [
                    'procedimiento_programacion_id',
                    'estado',
                ],
                'procedimiento_cupos_estado_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimiento_cupos');
    }
};
