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
        Schema::create('camas_pacientes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_paciente_sigh')->nullable()->index();

            $table->string('nro_historia_clinica', 50)->nullable()->index();

            $table->string('nombre_completo', 300);

            $table->string('telefono', 50)->nullable();

            $table->timestamps();

            $table->unique(
                'id_paciente_sigh',
                'uq_camas_paciente_sigh'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camas_pacientes');
    }
};
