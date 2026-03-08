<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajeria.participantes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversacion_id');
            $table->uuid('usuario_id');
            $table->boolean('es_admin')->default(false);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->string('estado', 15)->default('activo'); // activo, silenciado, salido
            $table->timestamps();

            $table->foreign('conversacion_id')->references('id')->on('mensajeria.conversaciones')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->unique(['conversacion_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajeria.participantes');
    }
};
