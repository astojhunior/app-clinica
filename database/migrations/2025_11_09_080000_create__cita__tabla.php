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
            Schema::create('citas', function (Blueprint $table) {
                $table->string('IdCita', 20)->primary(); // O puedes usar ->id() si prefieres autoincrement
                $table->date('FechaCita');
                $table->time('HoraInicio');
                $table->time('HoraFin')->nullable();
                $table->string('Estado', 45); // Programada, Completada, Cancelada
                $table->string('MotivoCita', 200)->nullable();
                $table->string('Modalidad', 45); // Presencial, Virtual

                // --- TUS NUEVOS CAMPOS SOLICITADOS ---
                $table->decimal('CostoTotal', 10, 2); // Se guarda el precio al momento de crear (Snapshot)
                $table->string('EstadoPago', 20)->default('Pendiente'); // Pendiente, Parcial, Pagado

                // Relaciones
                $table->string('Paciente_idPaciente', 20); // Asegúrate que coincida con tu PK de pacientes
                $table->string('Doctor_idDoctores', 20);   // Asegúrate que coincida con tu PK de doctores
                $table->string('Tipo_Cita_idTipo_Cita', 20);

                $table->timestamps();

                // Claves Foráneas
                $table->foreign('Paciente_idPaciente')->references('IdPaciente')->on('pacientes');
                $table->foreign('Doctor_idDoctores')->references('IdDoctores')->on('doctores');
                $table->foreign('Tipo_Cita_idTipo_Cita')->references('IdTipo_Cita')->on('tipo_citas');

                // NOTA: ¡Aquí YA NO VA Pago_idPago!
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_cita__tabla');
    }
};
