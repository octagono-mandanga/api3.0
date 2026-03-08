<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.perfiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->uuid('institucion_id');
            $table->uuid('sede_id')->nullable();
            $table->smallInteger('rol_id');
            $table->boolean('es_principal')->default(false);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('sede_id')->references('id')->on('core.sedes')->onDelete('set null');
            $table->foreign('rol_id')->references('id')->on('auth.roles');
            $table->unique(['usuario_id', 'institucion_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.perfiles');
    }
};
