<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.asignaturas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('area_id')->nullable();
            $table->string('nombre', 80);
            $table->string('codigo', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->smallInteger('horas_semanales')->nullable();
            $table->boolean('es_obligatoria')->default(true);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('academico.areas_formacion');
            $table->unique(['institucion_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.asignaturas');
    }
};
