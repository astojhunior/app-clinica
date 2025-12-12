<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajusta el tipo al mismo que uses en empleados.IdEmpleado (por ejemplo string, length 50)
            $table->string('Empleado_idEmpleado', 20)
                  ->nullable()
                  ->after('password');

            // Llave foránea hacia empleados
            $table->foreign('Empleado_idEmpleado', 'fk_users_empleados')
                  ->references('IdEmpleado')
                  ->on('empleados')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_users_empleados');
            $table->dropColumn('Empleado_idEmpleado');
        });
    }
};
