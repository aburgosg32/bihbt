<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camas_pacientes', function (Blueprint $table) {

            $table->string('sexo', 20)
                ->nullable()
                ->after('nombre_completo');

            $table->date('fecha_nacimiento')
                ->nullable()
                ->after('sexo');
        });
    }

    public function down(): void
    {
        Schema::table('camas_pacientes', function (Blueprint $table) {

            $table->dropColumn([
                'sexo',
                'fecha_nacimiento',
            ]);
        });
    }
};
