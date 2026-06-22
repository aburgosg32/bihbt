<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('radiologia_citas', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_cuenta_atencion')->index();

            $table->string('paciente', 200);
            $table->string('nro_historia_clinica', 50)->nullable();
            $table->string('telefono', 50)->nullable();

            $table->date('fecha_nacimiento')->nullable();
            $table->integer('edad')->nullable();
            $table->string('tipo_edad', 20)->nullable();

            $table->date('fecha_ingreso')->nullable();
            $table->string('hora_ingreso', 20)->nullable();

            $table->string('tipo_servicio', 100)->nullable();
            $table->string('nombre_servicio', 150)->nullable();
            $table->string('fuente_financiamiento', 100)->nullable();

            $table->enum('modalidad', [
                'RAYOS X',
                'TOMOGRAFIA',
                'ECOGRAFIA'
            ]);

            $table->string('estudio', 200)->nullable();

            $table->date('fecha_cita');
            $table->time('hora_cita')->nullable();

            $table->enum('estado', [
                'REGISTRADO',
                'ATENDIDO',
                'CANCELADO',
                'NO ASISTIO'
            ])->default('REGISTRADO');

            $table->text('observacion')->nullable();

            $table->unsignedBigInteger('usuario_registra_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiologia_citas');
    }
};
