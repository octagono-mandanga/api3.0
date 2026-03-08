<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horario.eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('sede_id')->nullable();
            $table->smallInteger('tipo_id')->nullable();
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->boolean('todo_el_dia')->default(false);
            $table->string('ubicacion', 100)->nullable();
            $table->string('visibilidad', 20)->default('todos'); // todos, docentes, estudiantes, padres
            $table->uuid('creador_id');
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('sede_id')->references('id')->on('core.sedes')->onDelete('set null');
            $table->foreign('tipo_id')->references('id')->on('horario.tipos_evento');
            $table->foreign('creador_id')->references('id')->on('auth.usuarios');

            $table->index(['institucion_id', 'fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario.eventos');
    }
};
