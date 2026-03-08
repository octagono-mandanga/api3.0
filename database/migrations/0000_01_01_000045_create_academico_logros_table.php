<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.logros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('competencia_id')->nullable();
            $table->uuid('asignatura_id')->nullable();
            $table->smallInteger('grado_id')->nullable();
            $table->string('descripcion', 500);
            $table->string('codigo', 20)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('competencia_id')->references('id')->on('academico.competencias')->onDelete('set null');
            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas')->onDelete('set null');
            $table->foreign('grado_id')->references('id')->on('core.grados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.logros');
    }
};
