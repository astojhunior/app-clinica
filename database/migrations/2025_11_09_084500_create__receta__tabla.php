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
        Schema::create('recetas', function (Blueprint $table) {
            $table->string('IdReceta', 20)->primary();
            $table->dateTime('FechaReceta');
            $table->string('Instrucciones', 200)->nullable();
            $table->string('Tratamiento_idTratamiento', 20);
            $table->timestamps();

            $table->foreign('Tratamiento_idTratamiento')->references('IdTratamiento')->on('tratamientos');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_receta__tabla');
    }
};
