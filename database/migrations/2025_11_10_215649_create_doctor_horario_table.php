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
        Schema::create('doctor_horario', function (Blueprint $table) {
            $table->id(); // ID propio de la relación

            // Las dos llaves foráneas que unen la relación N:M
            $table->string('Doctores_idDoctores', 20);
            $table->string('Horario_idHorario', 20);

            $table->timestamps();

            // Referencias (Constraints)
            $table->foreign('Doctores_idDoctores')
                ->references('IdDoctores')->on('doctores')
                ->onDelete('cascade');

            $table->foreign('Horario_idHorario')
                ->references('IdHorario')->on('horarios')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_horario');
    }
};
