<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quirofano_solicitudes', function (Blueprint $table) {
            $table->id();

            /*
             * Usuario y servicio que realizan la solicitud.
             */
            $table->foreignId('usuario_solicitante_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('servicio_solicitante', 150);

            /*
             * Datos obtenidos desde SIGH mediante número de cuenta.
             */
            $table->string('numero_cuenta', 30);
            $table->unsignedBigInteger('id_paciente_sigh')->nullable();
            $table->string('historia_clinica', 30)->nullable();
            $table->string('paciente', 200);
            $table->string('dni', 20)->nullable();
            $table->string('sexo', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->unsignedInteger('edad')->nullable();
            $table->string('tipo_seguro', 100)->nullable();
            $table->string('servicio_actual', 150)->nullable();
            $table->string('cama', 50)->nullable();
            $table->dateTime('fecha_ingreso')->nullable();

            /*
             * Médico responsable de la operación.
             */
            $table->string('medico_codigo', 30);
            $table->string('medico_nombre', 200);
            $table->string('medico_especialidad', 150)->nullable();

            /*
             * Diagnóstico preoperatorio.
             */
            $table->string('diagnostico_codigo', 30);
            $table->string('diagnostico_nombre', 500);

            /*
             * Procedimiento u operación solicitada.
             */
            $table->string('procedimiento_codigo', 30);
            $table->string('procedimiento_nombre', 500);

            /*
             * Información complementaria.
             */
            $table->enum('tipo_solicitud', [
                'ELECTIVA',
                'URGENCIA',
                'EMERGENCIA',
            ])->default('ELECTIVA');

            $table->enum('prioridad', [
                'BAJA',
                'MEDIA',
                'ALTA',
                'CRITICA',
            ])->default('MEDIA');

            $table->unsignedInteger('duracion_estimada_minutos')
                ->nullable();

            $table->string('tipo_anestesia', 100)->nullable();
            $table->date('fecha_sugerida')->nullable();
            $table->text('requerimientos_especiales')->nullable();
            $table->text('observaciones')->nullable();

            /*
             * Estado administrativo de la solicitud.
             */
            $table->enum('estado', [
                'BORRADOR',
                'ENVIADA',
                'EN_REVISION',
                'OBSERVADA',
                'APROBADA',
                'PROGRAMADA',
                'CANCELADA',
                'RECHAZADA',
                'FINALIZADA',
            ])->default('ENVIADA');

            $table->text('motivo_observacion')->nullable();
            $table->dateTime('fecha_envio')->nullable();
            $table->dateTime('fecha_revision')->nullable();

            $table->timestamps();

            $table->index('numero_cuenta');
            $table->index('historia_clinica');
            $table->index('estado');
            $table->index('servicio_solicitante');
            $table->index('fecha_sugerida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quirofano_solicitudes');
    }
};
