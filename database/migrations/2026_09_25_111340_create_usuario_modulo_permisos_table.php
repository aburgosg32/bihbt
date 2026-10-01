<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_modulo_permisos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('modulo', 50);
            $table->boolean('ver')->default(false);
            $table->boolean('gestionar')->default(false);

            $table->timestamps();

            $table->unique(['user_id', 'modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_modulo_permisos');
    }
};
