<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion.docentes_asignatura', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id'); // docente
            $table->uuid('asignatura_id');
            $table->uuid('curso_id');
            $table->uuid('lectivo_id');
            $table->boolean('es_titular')->default(true);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('inscripcion.cursos')->onDelete('cascade');
            $table->foreign('lectivo_id')->references('id')->on('core.lectivos')->onDelete('cascade');
            $table->unique(['usuario_id', 'asignatura_id', 'curso_id', 'lectivo_id'], 'docentes_asignatura_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion.docentes_asignatura');
    }
};
