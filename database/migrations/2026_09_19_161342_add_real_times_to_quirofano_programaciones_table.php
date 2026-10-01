<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'quirofano_programaciones',
            function (Blueprint $table) {
                $table->dateTime(
                    'fecha_inicio_preparacion'
                )
                    ->nullable()
                    ->after('estado');

                $table->dateTime(
                    'fecha_inicio_real'
                )
                    ->nullable()
                    ->after(
                        'fecha_inicio_preparacion'
                    );

                $table->dateTime(
                    'fecha_fin_real'
                )
                    ->nullable()
                    ->after('fecha_inicio_real');

                $table->dateTime(
                    'fecha_fin_limpieza'
                )
                    ->nullable()
                    ->after('fecha_fin_real');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'quirofano_programaciones',
            function (Blueprint $table) {
                $table->dropColumn([
                    'fecha_inicio_preparacion',
                    'fecha_inicio_real',
                    'fecha_fin_real',
                    'fecha_fin_limpieza',
                ]);
            }
        );
    }
};
