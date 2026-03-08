<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.sedes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('municipio_id')->nullable();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->nullable();
            $table->boolean('es_principal')->default(false);
            $table->string('direccion', 150)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('municipio_id')->references('id')->on('ref.municipios');
            $table->unique(['institucion_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.sedes');
    }
};
