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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->string('IdPaciente', 20)->primary();
            $table->string('Nombres', 45);
            $table->string('Apellidos', 45);
            $table->string('Dni', 45);
            $table->string('CorreoElectronico', 45);
            $table->dateTime('FechaNacimiento');
            $table->string('Genero', 45);
            $table->string('Direccion', 200);
            $table->string('Telefono', 45);
            $table->tinyInteger('Estado');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_paciente__tabla');
    }
};
