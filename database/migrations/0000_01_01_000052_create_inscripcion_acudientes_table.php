<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripcion.acudientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->uuid('estudiante_id');
            $table->smallInteger('parentesco_id')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->boolean('autorizado_recoger')->default(true);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->foreign('estudiante_id')->references('id')->on('inscripcion.estudiantes')->onDelete('cascade');
            $table->foreign('parentesco_id')->references('id')->on('inscripcion.tipos_parentesco');
            $table->unique(['usuario_id', 'estudiante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion.acudientes');
    }
};
