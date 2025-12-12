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
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->string('IdDiagnostics', 20)->primary();
            $table->string('Descripcion', 200);
            $table->string('Tipo', 45); // Presuntivo, Definitivo

            // ✅ Relación: El Diagnóstico pertenece a una Consulta
            $table->string('Consulta_idConsulta', 20);
            $table->foreign('Consulta_idConsulta')->references('IdConsulta')->on('consultas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_diagnosticos__tabla');
    }
};
