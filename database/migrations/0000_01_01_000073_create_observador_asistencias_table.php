<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observador.asistencias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('matricula_id');
            $table->uuid('curso_id');
            $table->uuid('asignatura_id')->nullable();
            $table->date('fecha');
            $table->boolean('presente')->default(true);
            $table->smallInteger('tipo_ausencia_id')->nullable();
            $table->boolean('justificada')->default(false);
            $table->string('justificacion', 255)->nullable();
            $table->uuid('registrado_por')->nullable();
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('matricula_id')->references('id')->on('inscripcion.matriculas')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('inscripcion.cursos');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas');
            $table->foreign('tipo_ausencia_id')->references('id')->on('observador.tipos_ausencia');
            $table->foreign('registrado_por')->references('id')->on('auth.usuarios')->onDelete('set null');
            $table->unique(['matricula_id', 'fecha', 'asignatura_id'], 'asistencias_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observador.asistencias');
    }
};
