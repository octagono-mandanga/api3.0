<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.niveles_institucion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('nivel_id');
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('nivel_id')->references('id')->on('core.niveles_educativos');
            $table->unique(['institucion_id', 'nivel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.niveles_institucion');
    }
};
