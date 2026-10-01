<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quirofano_salas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->unique();
            $table->string('codigo', 20)->unique();

            $table->string('ubicacion', 150)->nullable();

            /*
             * DISPONIBLE
             * PROGRAMADA
             * EN_PREPARACION
             * EN_USO
             * EN_LIMPIEZA
             * MANTENIMIENTO
             */
            $table->string('estado_actual', 30)
                ->default('DISPONIBLE');

            /*
             * true  = puede utilizarse y programarse
             * false = sala inoperativa
             */
            $table->boolean('operativa')
                ->default(true);

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quirofano_salas');
    }
};
