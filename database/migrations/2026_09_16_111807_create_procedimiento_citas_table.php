<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'procedimiento_citas',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('procedimiento_cupo_id')
                    ->constrained('procedimiento_cupos')
                    ->restrictOnDelete();

                /*
                 * Datos obtenidos desde SIGH.
                 */
                $table->string('numero_cuenta', 30);

                $table->string('id_paciente_sigh', 30)
                    ->nullable();

                $table->string('historia_clinica', 30)
                    ->nullable();

                $table->string('paciente', 200);

                $table->string('telefono', 30)
                    ->nullable();

                $table->string('sexo', 20)
                    ->nullable();

                $table->date('fecha_nacimiento')
                    ->nullable();

                $table->string('servicio_origen', 150)
                    ->nullable();

                $table->string('tipo_servicio', 100)
                    ->nullable();

                $table->string('financiamiento', 150)
                    ->nullable();

                /*
                 * Control de la cita.
                 */
                $table->string('estado', 20)
                    ->default('PROGRAMADA');

                $table->text('observacion')
                    ->nullable();

                $table->timestamp('fecha_registro')
                    ->useCurrent();

                $table->timestamps();

                /*
                 * Un cupo solo puede tener una cita.
                 */
                $table->unique(
                    'procedimiento_cupo_id',
                    'procedimiento_citas_cupo_unique'
                );

                $table->index(
                    'numero_cuenta',
                    'procedimiento_citas_cuenta_index'
                );

                $table->index(
                    [
                        'estado',
                        'fecha_registro',
                    ],
                    'procedimiento_citas_estado_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimiento_citas');
    }
};
