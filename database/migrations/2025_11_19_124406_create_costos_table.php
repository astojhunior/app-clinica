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
        Schema::create('costos', function (Blueprint $table) {
            $table->id('idCosto'); // Autoincremental
            $table->decimal('Monto', 10, 2);
            $table->date('FechaInicioVigencia');
            $table->date('FechaFinVigencia')->nullable();

            // Relación con TIPO DE CITA
            $table->string('Tipo_Cita_idTipo_Cita', 20);

            $table->timestamps();

            // Clave Foránea
            $table->foreign('Tipo_Cita_idTipo_Cita')->references('IdTipo_Cita')->on('tipo_citas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costos');
    }
};
