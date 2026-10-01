<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'procedimiento_citas',
            function (Blueprint $table) {
                $table->unique(
                    'numero_cuenta',
                    'procedimiento_citas_numero_cuenta_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'procedimiento_citas',
            function (Blueprint $table) {
                $table->dropUnique(
                    'procedimiento_citas_numero_cuenta_unique'
                );
            }
        );
    }
};
