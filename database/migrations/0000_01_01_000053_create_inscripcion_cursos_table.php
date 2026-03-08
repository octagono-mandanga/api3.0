<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion.cursos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('sede_id')->nullable();
            $table->uuid('lectivo_id');
            $table->smallInteger('grado_id');
            $table->smallInteger('jornada_id')->nullable();
            $table->string('nombre', 50);
            $table->string('codigo', 20)->nullable();
            $table->uuid('director_id')->nullable(); // usuario docente director de grupo
            $table->integer('capacidad')->nullable();
            $table->string('aula', 30)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('sede_id')->references('id')->on('core.sedes')->onDelete('set null');
            $table->foreign('lectivo_id')->references('id')->on('core.lectivos')->onDelete('cascade');
            $table->foreign('grado_id')->references('id')->on('core.grados');
            $table->foreign('jornada_id')->references('id')->on('core.jornadas');
            $table->foreign('director_id')->references('id')->on('auth.usuarios')->onDelete('set null');
            $table->unique(['institucion_id', 'lectivo_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion.cursos');
    }
};
