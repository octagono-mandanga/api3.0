<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.periodos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('lectivo_id');
            $table->smallInteger('numero');
            $table->string('nombre', 50);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('peso', 5, 2)->default(25.00); // porcentaje
            $table->boolean('es_activo')->default(false);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('lectivo_id')->references('id')->on('core.lectivos')->onDelete('cascade');
            $table->unique(['lectivo_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.periodos');
    }
};
