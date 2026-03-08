<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega columnas de geolocalización a core.sedes.
     * La ubicación pertenece a cada sede, no a la institución directamente.
     */
    public function up(): void
    {
        Schema::table('core.sedes', function (Blueprint $table) {
            // Latitud: -90 a +90 con 8 decimales (~1.1 mm de precisión)
            $table->decimal('latitud', 10, 8)->nullable()->after('direccion');
            // Longitud: -180 a +180 con 8 decimales
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
        });
    }

    public function down(): void
    {
        Schema::table('core.sedes', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};
