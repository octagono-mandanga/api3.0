<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.competencias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('tipo_id')->nullable();
            $table->uuid('asignatura_id')->nullable();
            $table->string('nombre', 150);
            $table->string('codigo', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('tipo_id')->references('id')->on('academico.tipos_competencia');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.competencias');
    }
};
