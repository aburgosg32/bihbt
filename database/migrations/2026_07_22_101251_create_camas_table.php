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
        Schema::create('camas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servicio_id')
                ->constrained('camas_servicios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('habitacion_id')
                ->nullable()
                ->constrained('camas_habitaciones')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('codigo', 50)->unique();

            $table->string('numero', 50);

            $table->string('tipo_cama', 100)
                ->nullable();

            $table->enum('estado_operativo', [
                'OPERATIVA',
                'LIMPIEZA',
                'MANTENIMIENTO',
                'BLOQUEADA',
                'FUERA_DE_SERVICIO'
            ])->default('OPERATIVA');

            $table->enum('sexo_permitido', [
                'MASCULINO',
                'FEMENINO',
                'INDISTINTO'
            ])->default('INDISTINTO');

            $table->text('observaciones')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index('servicio_id');
            $table->index('habitacion_id');
            $table->index('estado_operativo');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camas');
    }
};
