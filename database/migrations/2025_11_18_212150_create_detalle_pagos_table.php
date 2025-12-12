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
            Schema::create('detalle_pagos', function (Blueprint $table) {
                $table->id('IdDetallePago'); // Autoincremental está bien aquí
                $table->string('Descripcion', 200); // Ej: "Adelanto Consulta"
                $table->decimal('Monto', 10, 2);
                $table->integer('Cantidad')->default(1);

                // Relación
                $table->string('Pago_idPago', 20);
                $table->foreign('Pago_idPago')->references('IdPago')->on('pagos')->onDelete('cascade');

                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pagos');
    }
};
