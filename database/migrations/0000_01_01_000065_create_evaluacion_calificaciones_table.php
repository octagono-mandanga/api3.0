<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.calificaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('actividad_id');
            $table->uuid('matricula_id');
            $table->decimal('nota', 4, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamp('fecha_calificacion')->nullable();
            $table->boolean('entregado')->default(false);
            $table->timestamp('fecha_entrega')->nullable();
            $table->string('estado', 15)->default('pendiente'); // pendiente, calificado, recuperacion
            $table->timestamps();

            $table->foreign('actividad_id')->references('id')->on('evaluacion.actividades')->onDelete('cascade');
            $table->foreign('matricula_id')->references('id')->on('inscripcion.matriculas')->onDelete('cascade');
            $table->unique(['actividad_id', 'matricula_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.calificaciones');
    }
};
