<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria.logs_actividad', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id')->nullable();
            $table->uuid('usuario_id')->nullable();
            $table->string('accion', 50); // crear, actualizar, eliminar, login, logout
            $table->string('entidad', 80)->nullable(); // nombre del modelo
            $table->uuid('entidad_id')->nullable();
            $table->jsonb('datos_anteriores')->nullable();
            $table->jsonb('datos_nuevos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at');

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('set null');
            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('set null');

            $table->index(['institucion_id', 'created_at']);
            $table->index(['usuario_id', 'created_at']);
            $table->index(['entidad', 'entidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria.logs_actividad');
    }
};
