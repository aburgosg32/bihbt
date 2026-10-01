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
        Schema::create('camas_habitaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servicio_id')
                ->constrained('camas_servicios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre', 100);
            $table->string('numero', 30)->nullable();

            $table->string('piso', 50)->nullable();

            $table->text('observaciones')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(
                ['servicio_id', 'nombre'],
                'uq_camas_habitacion_servicio_nombre'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camas_habitaciones');
    }
};
