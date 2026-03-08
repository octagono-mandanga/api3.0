<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.areas_institucion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('area_id');
            $table->smallInteger('nivel_id')->nullable()->comment('Si es null, aplica a todos los niveles');
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('academico.areas_formacion')->onDelete('cascade');
            $table->foreign('nivel_id')->references('id')->on('core.niveles_educativos')->onDelete('cascade');

            // Una institución puede tener un área asociada a un nivel específico solo una vez
            $table->unique(['institucion_id', 'area_id', 'nivel_id'], 'areas_inst_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.areas_institucion');
    }
};
