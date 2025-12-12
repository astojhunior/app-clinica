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
       Schema::create('tipo_citas', function (Blueprint $table) {
            $table->string('IdTipo_Cita', 20)->primary();
            $table->string('Descripcion', 200);
            $table->tinyInteger('Estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_tipo__cita__tabla');
    }
};
