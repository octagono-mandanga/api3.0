<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajeria.mensajes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversacion_id');
            $table->uuid('remitente_id');
            $table->text('contenido');
            $table->string('tipo', 20)->default('texto'); // texto, archivo, imagen
            $table->string('archivo_url', 255)->nullable();
            $table->uuid('mensaje_padre_id')->nullable(); // para respuestas directas
            $table->boolean('editado')->default(false);
            $table->timestamp('fecha_envio');
            $table->string('estado', 15)->default('enviado');
            $table->timestamps();

            $table->foreign('conversacion_id')->references('id')->on('mensajeria.conversaciones')->onDelete('cascade');
            $table->foreign('remitente_id')->references('id')->on('auth.usuarios');
            $table->foreign('mensaje_padre_id')->references('id')->on('mensajeria.mensajes')->onDelete('set null');

            $table->index(['conversacion_id', 'fecha_envio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajeria.mensajes');
    }
};
