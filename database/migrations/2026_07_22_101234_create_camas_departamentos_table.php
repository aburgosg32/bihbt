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
        Schema::create('camas_departamentos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);
            $table->string('codigo', 30)->nullable();

            $table->text('descripcion')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camas_departamentos');
    }
};
