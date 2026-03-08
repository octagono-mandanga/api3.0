<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS ref');
        DB::statement('CREATE SCHEMA IF NOT EXISTS auth');
        DB::statement('CREATE SCHEMA IF NOT EXISTS core');
        DB::statement('CREATE SCHEMA IF NOT EXISTS academico');
        DB::statement('CREATE SCHEMA IF NOT EXISTS inscripcion');
        DB::statement('CREATE SCHEMA IF NOT EXISTS evaluacion');
        DB::statement('CREATE SCHEMA IF NOT EXISTS observador');
        DB::statement('CREATE SCHEMA IF NOT EXISTS mensajeria');
        DB::statement('CREATE SCHEMA IF NOT EXISTS notificacion');
        DB::statement('CREATE SCHEMA IF NOT EXISTS horario');
        DB::statement('CREATE SCHEMA IF NOT EXISTS auditoria');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS auditoria CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS horario CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS notificacion CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS mensajeria CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS observador CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS evaluacion CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS inscripcion CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS academico CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS core CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS auth CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS ref CASCADE');
    }
};
