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
        Schema::create('consulta_sintoma', function (Blueprint $table) {
            $table->id(); // ID único de esta relación

            // 1. Clave que apunta a la CONSULTA
            $table->string('Consulta_idConsulta', 20);

            // 2. Clave que apunta al SÍNTOMA
            $table->string('Sintoma_idSintomas', 20);

            $table->timestamps();

            // 3. Definición de las relaciones (Constraints)
            // Si borras la consulta, se borran sus relaciones de síntomas (cascade)
            $table->foreign('Consulta_idConsulta')
                  ->references('IdConsulta')
                  ->on('consultas')
                  ->onDelete('cascade');

            // Si borras un síntoma del catálogo, se borra de las consultas (cascade)
            $table->foreign('Sintoma_idSintomas')
                  ->references('IdSintomas')
                  ->on('sintomas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulta_sintoma');
    }
};
