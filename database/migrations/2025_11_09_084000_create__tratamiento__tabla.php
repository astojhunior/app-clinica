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
       Schema::create('tratamientos', function (Blueprint $table) {
            $table->string('IdTratamiento', 20)->primary();
            $table->string('Descripcion', 200);
            $table->string('Indicaciones', 200)->nullable();
            $table->string('Consulta_idConsulta', 20);
            $table->string('Medicina_idMedicina', 20);
            $table->timestamps();

            $table->foreign('Consulta_idConsulta')->references('IdConsulta')->on('consultas');
            $table->foreign('Medicina_idMedicina')->references('IdMedicina')->on('medicinas');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_tratamiento__tabla');
    }
};
