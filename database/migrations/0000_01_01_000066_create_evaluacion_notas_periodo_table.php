<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.notas_periodo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('matricula_id');
            $table->uuid('asignatura_id');
            $table->uuid('periodo_id');
            $table->decimal('nota_definitiva', 4, 2)->nullable();
            $table->decimal('nota_recuperacion', 4, 2)->nullable();
            $table->decimal('nota_final', 4, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->uuid('logro_id')->nullable();
            $table->boolean('aprobado')->nullable();
            $table->timestamps();

            $table->foreign('matricula_id')->references('id')->on('inscripcion.matriculas')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas');
            $table->foreign('periodo_id')->references('id')->on('evaluacion.periodos');
            $table->foreign('logro_id')->references('id')->on('academico.logros')->onDelete('set null');
            $table->unique(['matricula_id', 'asignatura_id', 'periodo_id'], 'notas_periodo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.notas_periodo');
    }
};
