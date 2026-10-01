<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimiento_medicos', function (Blueprint $table) {
            $table->id();

            $table->string('numero_colegiatura', 30)
                ->nullable()
                ->unique();

            $table->string('apellido_paterno', 80);

            $table->string('apellido_materno', 80)
                ->nullable();

            $table->string('nombres', 120);

            $table->string('especialidad', 150)
                ->nullable();

            $table->string('telefono', 20)
                ->nullable();

            $table->string('correo', 150)
                ->nullable();

            $table->boolean('activo')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['apellido_paterno', 'apellido_materno'],
                'procedimiento_medicos_apellidos_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimiento_medicos');
    }
};
