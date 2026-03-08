<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El NIT no está disponible al momento de crear la institución desde la solicitud.
 * Se completa durante el wizard de configuración inicial. Por eso debe ser nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Quitar el NOT NULL directamente en PostgreSQL
        // (->change() intenta recrear el índice único y falla si ya existe)
        DB::statement('ALTER TABLE "core"."instituciones" ALTER COLUMN "nit" DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE "core"."instituciones" ALTER COLUMN "nit" SET NOT NULL');
    }
};
