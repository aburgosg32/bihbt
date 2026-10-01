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
        Schema::create('cama_ocupaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->constrained('camas_pacientes')
                ->restrictOnDelete();

            $table->foreignId('cama_id')
                ->constrained('camas')
                ->restrictOnDelete();

            $table->dateTime('fecha_hora_ingreso');

            $table->dateTime('fecha_hora_alta')
                ->nullable();

            $table->enum('estado', [
                'ACTIVO',
                'ALTA'
            ])->default('ACTIVO');

            $table->unsignedBigInteger('usuario_ingreso_id')
                ->nullable();

            $table->unsignedBigInteger('usuario_alta_id')
                ->nullable();

            $table->timestamps();

            $table->index([
                'cama_id',
                'estado'
            ]);

            $table->index([
                'paciente_id',
                'estado'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cama_ocupaciones');
    }
};
