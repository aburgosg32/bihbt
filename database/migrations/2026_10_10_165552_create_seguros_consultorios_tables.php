<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plantilla: consultorio habitual por servicio de SIGH.
        Schema::connection('mysql')->create(
            'seguros_consultorios_habituales',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedInteger('id_servicio_sigh');
                $table->string('numero_consultorio', 20);

                $table->timestamps();

                $table->unique(
                    'id_servicio_sigh',
                    'seg_cons_habitual_servicio_uq'
                );
            }
        );

        // Asignación efectiva para cada programación y fecha.
        Schema::connection('mysql')->create(
            'seguros_consultorios_asignaciones',
            function (Blueprint $table) {
                $table->id();

                $table->date('fecha');
                $table->unsignedInteger('id_programacion_sigh');
                $table->string('numero_consultorio', 20);

                $table->timestamps();

                $table->unique(
                    ['fecha', 'id_programacion_sigh'],
                    'seg_cons_asignacion_fecha_prog_uq'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(
            'seguros_consultorios_asignaciones'
        );

        Schema::connection('mysql')->dropIfExists(
            'seguros_consultorios_habituales'
        );
    }
};
