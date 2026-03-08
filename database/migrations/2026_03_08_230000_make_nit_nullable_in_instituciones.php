<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El NIT no está disponible al momento de crear la institución desde la solicitud.
 * Se completa durante el wizard de configuración inicial. Por eso debe ser nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core.instituciones', function (Blueprint $table) {
            $table->string('nit', 20)->nullable()->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('core.instituciones', function (Blueprint $table) {
            $table->string('nit', 20)->unique()->change();
        });
    }
};
