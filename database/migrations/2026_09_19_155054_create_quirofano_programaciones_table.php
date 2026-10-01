<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'quirofano_programaciones',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId(
                    'quirofano_solicitud_id'
                )
                    ->unique()
                    ->constrained(
                        'quirofano_solicitudes'
                    )
                    ->cascadeOnDelete();

                $table->foreignId(
                    'quirofano_sala_id'
                )
                    ->constrained(
                        'quirofano_salas'
                    )
                    ->restrictOnDelete();

                $table->dateTime(
                    'fecha_hora_inicio'
                );

                $table->dateTime(
                    'fecha_hora_fin'
                );

                $table->dateTime(
                    'fecha_hora_disponible'
                );

                $table->unsignedInteger(
                    'duracion_operacion_minutos'
                );

                $table->unsignedSmallInteger(
                    'minutos_limpieza'
                )->default(45);

                $table->enum('estado', [
                    'PROGRAMADA',
                    'EN_PREPARACION',
                    'EN_CURSO',
                    'FINALIZADA',
                    'CANCELADA',
                ])->default('PROGRAMADA');

                $table->foreignId(
                    'usuario_programador_id'
                )
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->text('observaciones')
                    ->nullable();

                $table->text('motivo_cancelacion')
                    ->nullable();

                $table->dateTime('fecha_cancelacion')
                    ->nullable();

                $table->timestamps();

                $table->index(
                    [
                        'quirofano_sala_id',
                        'fecha_hora_inicio',
                    ],
                    'idx_quirofano_sala_inicio'
                );

                $table->index(
                    [
                        'quirofano_sala_id',
                        'fecha_hora_fin',
                    ],
                    'idx_quirofano_sala_fin'
                );

                $table->index('estado');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'quirofano_programaciones'
        );
    }
};
