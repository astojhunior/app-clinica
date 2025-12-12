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
    Schema::create('empleados', function (Blueprint $table) {
        $table->string('IdEmpleado', 20)->primary();
        $table->string('Nombres', 45);
        $table->string('Apellidos', 45);
        $table->string('Dni', 45);
        $table->string('CorreElec', 45)->nullable();
        $table->date('FechaIngreso');
        $table->date('Fechasalida')->nullable();
        $table->tinyInteger('Estado');
        $table->string('Genero', 45);
        $table->date('FechaNacimiento');

        // Relación con Cargo (ESTA SÍ SE QUEDA)
        $table->string('Cargo_idCargo', 20);


        $table->timestamps();

        // Clave foránea de Cargo
        $table->foreign('Cargo_idCargo')->references('IdCargo')->on('cargos');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
