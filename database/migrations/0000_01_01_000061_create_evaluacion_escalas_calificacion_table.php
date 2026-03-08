<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.escalas_calificacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->string('nombre', 50);
            $table->decimal('nota_minima', 4, 2);
            $table->decimal('nota_maxima', 4, 2);
            $table->decimal('nota_aprobatoria', 4, 2);
            $table->boolean('usa_decimales')->default(true);
            $table->smallInteger('decimales')->default(1);
            $table->boolean('es_default')->default(false);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.escalas_calificacion');
    }
};
