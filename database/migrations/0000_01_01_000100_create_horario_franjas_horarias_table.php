<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horario.franjas_horarias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('jornada_id')->nullable();
            $table->string('nombre', 30);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->smallInteger('orden')->default(0);
            $table->boolean('es_descanso')->default(false);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('jornada_id')->references('id')->on('core.jornadas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario.franjas_horarias');
    }
};
