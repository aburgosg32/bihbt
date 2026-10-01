<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procedimiento_servicio_id')
                ->constrained('procedimiento_servicios')
                ->restrictOnDelete();

            $table->string('codigo', 30)
                ->unique();

            $table->string('nombre', 150);

            $table->text('descripcion')
                ->nullable();

            $table->unsignedSmallInteger('duracion_minutos')
                ->default(15);

            $table->string('color', 20)
                ->default('#007bff');

            $table->text('indicaciones')
                ->nullable();

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['procedimiento_servicio_id', 'nombre'],
                'procedimientos_servicio_nombre_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimientos');
    }
};
