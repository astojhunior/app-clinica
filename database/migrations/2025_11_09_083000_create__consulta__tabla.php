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
        Schema::create('consultas', function (Blueprint $table) {
            $table->string('IdConsulta', 20)->primary();
            $table->dateTime('FechaConsulta');
            $table->string('Sintomas', 200)->nullable(); // O FK a tabla sintomas si prefieres
            $table->string('ExploracionFisica', 200)->nullable();

            // Relación con la Cita (Padre)
            // Asegúrate de usar el mismo tipo de dato que en 'citas' (string/int)
            $table->string('Cita_idCita', 20);
            $table->foreign('Cita_idCita')->references('IdCita')->on('citas');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_consulta__tabla');
    }
};
