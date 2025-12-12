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
        Schema::create('medicinas', function (Blueprint $table) {
            $table->string('IdMedicina', 20)->primary();
            $table->string('Nombre', 45);
            $table->tinyInteger('Estado');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_medicina__tabla');
    }
};
