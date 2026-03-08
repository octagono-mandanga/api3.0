<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.notas_finales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('matricula_id');
            $table->uuid('asignatura_id');
            $table->decimal('nota_definitiva', 4, 2)->nullable();
            $table->decimal('nota_habilitacion', 4, 2)->nullable();
            $table->decimal('nota_final', 4, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->boolean('aprobado')->nullable();
            $table->timestamps();

            $table->foreign('matricula_id')->references('id')->on('inscripcion.matriculas')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas');
            $table->unique(['matricula_id', 'asignatura_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.notas_finales');
    }
};
