<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla para calendarios diferenciados por nivel educativo
        // Permite configurar fechas de inicio/fin diferentes por nivel cuando
        // mismoPeriodoParaTodos = false en el frontend
        Schema::create('core.lectivos_nivel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lectivo_id');
            $table->smallInteger('nivel_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('lectivo_id')->references('id')->on('core.lectivos')->onDelete('cascade');
            $table->foreign('nivel_id')->references('id')->on('core.niveles_educativos')->onDelete('cascade');

            // Un lectivo solo puede tener una configuración por nivel
            $table->unique(['lectivo_id', 'nivel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.lectivos_nivel');
    }
};
