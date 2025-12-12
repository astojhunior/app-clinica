<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('TipoPago_id')->nullable()->after('MontoTotal');
            $table->foreign('TipoPago_id')->references('IDtipos')->on('tipos_pagos');
            // Elimina la columna antigua si existe:
            // $table->dropColumn('MetodoPago');
        });
    }

    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['TipoPago_id']);
            $table->dropColumn('TipoPago_id');
        });
    }
};
