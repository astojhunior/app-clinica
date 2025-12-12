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
        Schema::create('doctores', function (Blueprint $table) {
            $table->string('IdDoctores', 20)->primary(); // Tu PK
            $table->string('Numero_Colegiatura', 45);
            $table->string('Biografia', 200)->nullable();

            // Relaciones que SÍ deben estar
            $table->string('Especialidad_idEspecialidad', 20);
            $table->string('Empleado_idEmpleado', 20);

            // ❌ AQUÍ BORRAMOS 'Horario_idHorario'. ¡Ya no debe existir!

            $table->timestamps();

            // Definición de claves foráneas
            $table->foreign('Especialidad_idEspecialidad')
                ->references('IdEspecialidad')->on('especialidades');

            $table->foreign('Empleado_idEmpleado')
                ->references('IdEmpleado')->on('empleados')
                ->onDelete('cascade'); // Recomendado: Si borras al empleado, se borra el doctor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_doctores__tabla');
    }
};
