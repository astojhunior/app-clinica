<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipos_pagos', function (Blueprint $table) {
            $table->id('IDtipos');
            $table->string('TipoDescripcion', 50);
            $table->boolean('TipoEstado')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipos_pagos');
    }
};

