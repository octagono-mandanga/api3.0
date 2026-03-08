<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.actividades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('asignatura_id');
            $table->uuid('curso_id');
            $table->uuid('periodo_id');
            $table->uuid('docente_id');
            $table->smallInteger('tipo_id')->nullable();
            $table->uuid('logro_id')->nullable();
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->date('fecha_asignacion');
            $table->date('fecha_entrega')->nullable();
            $table->decimal('peso', 5, 2)->nullable(); // porcentaje en el periodo
            $table->decimal('nota_maxima', 4, 2)->default(5.00);
            $table->boolean('permite_entrega_tardia')->default(false);
            $table->boolean('visible_estudiantes')->default(true);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas');
            $table->foreign('curso_id')->references('id')->on('inscripcion.cursos');
            $table->foreign('periodo_id')->references('id')->on('evaluacion.periodos');
            $table->foreign('docente_id')->references('id')->on('auth.usuarios');
            $table->foreign('tipo_id')->references('id')->on('evaluacion.tipos_actividad');
            $table->foreign('logro_id')->references('id')->on('academico.logros')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.actividades');
    }
};
