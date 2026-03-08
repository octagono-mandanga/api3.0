<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion.rangos_escala', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('escala_id');
            $table->decimal('desde', 4, 2);
            $table->decimal('hasta', 4, 2);
            $table->string('desempeno', 30); // Superior, Alto, Básico, Bajo
            $table->string('abreviatura', 5)->nullable();
            $table->string('color', 10)->nullable();
            $table->timestamps();

            $table->foreign('escala_id')->references('id')->on('evaluacion.escalas_calificacion')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion.rangos_escala');
    }
};
