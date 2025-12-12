<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            // Clave Primaria del Pago
            $table->string('IdPago', 20)->primary();

            // Datos del Pago
            $table->dateTime('FechaPago');
            $table->tinyInteger('EstadoPago'); // 1: Completado, 0: Anulado, etc.
            $table->decimal('MontoTotal', 10, 2);

            // --- LA RELACIÓN CORRECTA (El Pago apunta a la Cita) ---
            // Esto permite que una Cita tenga MÚLTIPLES Pagos
            $table->string('Cita_idCita', 20);

            $table->foreign('Cita_idCita')
                  ->references('IdCita')
                  ->on('citas')
                  ->onDelete('cascade'); // Si se borra la cita, se borran sus pagos
            // -------------------------------------------------------

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
