<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quirofano_salas', function (Blueprint $table) {
            $table->enum('tipo_sala', [
                'PROGRAMADA',
                'EMERGENCIA',
            ])
                ->default('PROGRAMADA')
                ->after('ubicacion');

            $table->time('hora_inicio_jornada')
                ->nullable()
                ->after('tipo_sala');

            $table->time('hora_fin_jornada')
                ->nullable()
                ->after('hora_inicio_jornada');

            $table->unsignedSmallInteger('minutos_limpieza')
                ->default(45)
                ->after('hora_fin_jornada');
        });

        /*
         * Salas 1 a 4:
         * operaciones programadas de 08:10 a 20:00.
         */
        DB::table('quirofano_salas')
            ->whereIn('codigo', [
                'SALA-01',
                'SALA-02',
                'SALA-03',
                'SALA-04',
            ])
            ->update([
                'tipo_sala' => 'PROGRAMADA',
                'hora_inicio_jornada' => '08:10:00',
                'hora_fin_jornada' => '20:00:00',
                'minutos_limpieza' => 45,
                'operativa' => true,
                'estado_actual' => 'DISPONIBLE',
            ]);

        /*
         * Salas 5 y 6:
         * emergencia, disponibles las 24 horas.
         *
         * Las horas permanecen en NULL porque el tipo
         * EMERGENCIA representa funcionamiento continuo.
         */
        DB::table('quirofano_salas')
            ->whereIn('codigo', [
                'SALA-05',
                'SALA-06',
            ])
            ->update([
                'tipo_sala' => 'EMERGENCIA',
                'hora_inicio_jornada' => null,
                'hora_fin_jornada' => null,
                'minutos_limpieza' => 45,
                'operativa' => true,
                'estado_actual' => 'DISPONIBLE',
            ]);

        /*
         * Las salas 7 y 8 no se eliminan.
         * Quedan inoperativas por no formar parte
         * de las seis salas actuales.
         */
        DB::table('quirofano_salas')
            ->whereIn('codigo', [
                'SALA-07',
                'SALA-08',
            ])
            ->update([
                'operativa' => false,
                'estado_actual' => 'MANTENIMIENTO',
                'observaciones' =>
                'Sala fuera de la configuración operativa actual.',
            ]);
    }

    public function down(): void
    {
        Schema::table('quirofano_salas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_sala',
                'hora_inicio_jornada',
                'hora_fin_jornada',
                'minutos_limpieza',
            ]);
        });
    }
};
