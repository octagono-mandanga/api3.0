<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horario.horarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('lectivo_id');
            $table->uuid('curso_id');
            $table->uuid('asignatura_id');
            $table->uuid('docente_id');
            $table->uuid('franja_id');
            $table->smallInteger('dia_semana'); // 1=Lunes, ..., 7=Domingo
            $table->string('aula', 30)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('lectivo_id')->references('id')->on('core.lectivos');
            $table->foreign('curso_id')->references('id')->on('inscripcion.cursos');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas');
            $table->foreign('docente_id')->references('id')->on('auth.usuarios');
            $table->foreign('franja_id')->references('id')->on('horario.franjas_horarias');

            $table->unique(['curso_id', 'franja_id', 'dia_semana'], 'horarios_curso_unique');
            $table->unique(['docente_id', 'franja_id', 'dia_semana', 'lectivo_id'], 'horarios_docente_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario.horarios');
    }
};
