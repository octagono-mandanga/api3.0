<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion.matriculas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('estudiante_id');
            $table->uuid('curso_id');
            $table->string('codigo_matricula', 30)->nullable();
            $table->date('fecha_matricula');
            $table->string('tipo', 30)->default('nuevo'); // nuevo, antiguo, traslado
            $table->boolean('repitente')->default(false);
            $table->string('estado', 15)->default('activo'); // activo, retirado, trasladado, graduado
            $table->timestamps();

            $table->foreign('estudiante_id')->references('id')->on('inscripcion.estudiantes')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('inscripcion.cursos')->onDelete('cascade');
            $table->unique(['estudiante_id', 'curso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion.matriculas');
    }
};
