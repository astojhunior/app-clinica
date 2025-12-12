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
        Schema::table('pagos', function (Blueprint $table) {
            // Agregamos SubTotal después de EstadoPago y antes de MontoTotal
            // Usamos nullable() por si ya tienes pagos registrados (para que no de error)
            $table->decimal('SubTotal', 10, 2)->after('EstadoPago')->nullable();

            // Agregamos MontoIGV después de SubTotal
            $table->decimal('MontoIGV', 10, 2)->after('SubTotal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['SubTotal', 'MontoIGV']);
        });
    }
};
