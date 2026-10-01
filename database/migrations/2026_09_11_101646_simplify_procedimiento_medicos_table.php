<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procedimiento_medicos', function (Blueprint $table) {
            $table->dropUnique(
                'procedimiento_medicos_numero_colegiatura_unique'
            );

            $table->dropColumn([
                'numero_colegiatura',
                'telefono',
                'correo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('procedimiento_medicos', function (Blueprint $table) {
            $table->string('numero_colegiatura', 30)
                ->nullable()
                ->unique();

            $table->string('telefono', 20)
                ->nullable();

            $table->string('correo', 150)
                ->nullable();
        });
    }
};
