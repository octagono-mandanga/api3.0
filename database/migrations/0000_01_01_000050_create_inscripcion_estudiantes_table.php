<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion.estudiantes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->uuid('institucion_id');
            $table->string('codigo_estudiante', 30)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->unique(['institucion_id', 'codigo_estudiante']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion.estudiantes');
    }
};
