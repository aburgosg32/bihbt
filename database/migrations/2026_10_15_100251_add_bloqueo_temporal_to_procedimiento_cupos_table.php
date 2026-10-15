<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'procedimiento_cupos',
            function (Blueprint $table) {
                $table->string(
                    'bloqueo_token',
                    64
                )->nullable()->after('motivo_bloqueo');

                $table->dateTime(
                    'bloqueado_hasta'
                )->nullable()->after('bloqueo_token');

                $table->index(
                    'bloqueo_token',
                    'procedimiento_cupos_bloqueo_token_index'
                );

                $table->index(
                    'bloqueado_hasta',
                    'procedimiento_cupos_bloqueado_hasta_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'procedimiento_cupos',
            function (Blueprint $table) {
                $table->dropIndex(
                    'procedimiento_cupos_bloqueo_token_index'
                );

                $table->dropIndex(
                    'procedimiento_cupos_bloqueado_hasta_index'
                );

                $table->dropColumn([
                    'bloqueo_token',
                    'bloqueado_hasta',
                ]);
            }
        );
    }
};
