<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajeria.conversaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->string('tipo', 20)->default('directa'); // directa, grupal
            $table->string('titulo', 100)->nullable();
            $table->uuid('creador_id');
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('creador_id')->references('id')->on('auth.usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajeria.conversaciones');
    }
};
