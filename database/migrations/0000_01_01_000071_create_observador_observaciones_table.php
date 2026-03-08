<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observador.observaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('matricula_id');
            $table->uuid('autor_id'); // usuario que reporta
            $table->smallInteger('tipo_id');
            $table->text('descripcion');
            $table->date('fecha');
            $table->text('compromiso')->nullable();
            $table->text('seguimiento')->nullable();
            $table->boolean('notificar_acudiente')->default(true);
            $table->boolean('visto_por_acudiente')->default(false);
            $table->timestamp('fecha_visto_acudiente')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('matricula_id')->references('id')->on('inscripcion.matriculas')->onDelete('cascade');
            $table->foreign('autor_id')->references('id')->on('auth.usuarios');
            $table->foreign('tipo_id')->references('id')->on('observador.tipos_observacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observador.observaciones');
    }
};
