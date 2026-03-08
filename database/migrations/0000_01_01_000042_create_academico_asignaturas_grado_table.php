<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.asignaturas_grado', function (Blueprint $table) {
            $table->id();
            $table->uuid('asignatura_id');
            $table->smallInteger('grado_id');
            $table->smallInteger('intensidad_horaria')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas')->onDelete('cascade');
            $table->foreign('grado_id')->references('id')->on('core.grados');
            $table->unique(['asignatura_id', 'grado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.asignaturas_grado');
    }
};
