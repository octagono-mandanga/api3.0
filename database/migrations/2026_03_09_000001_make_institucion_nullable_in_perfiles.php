<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core.perfiles', function (Blueprint $table) {
            // Eliminar la restricción de foreign key existente
            $table->dropForeign(['institucion_id']);

            // Hacer la columna nullable
            $table->uuid('institucion_id')->nullable()->change();

            // Recrear el foreign key permitiendo null
            $table->foreign('institucion_id')
                  ->references('id')
                  ->on('core.instituciones')
                  ->onDelete('cascade');
        });

        // Actualizar el unique constraint para permitir null en institucion_id
        Schema::table('core.perfiles', function (Blueprint $table) {
            $table->dropUnique(['usuario_id', 'institucion_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::table('core.perfiles', function (Blueprint $table) {
            $table->dropForeign(['institucion_id']);
            $table->uuid('institucion_id')->nullable(false)->change();
            $table->foreign('institucion_id')
                  ->references('id')
                  ->on('core.instituciones')
                  ->onDelete('cascade');
            $table->unique(['usuario_id', 'institucion_id', 'rol_id']);
        });
    }
};
