<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajeria.lecturas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mensaje_id');
            $table->uuid('usuario_id');
            $table->timestamp('leido_en');

            $table->foreign('mensaje_id')->references('id')->on('mensajeria.mensajes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->unique(['mensaje_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajeria.lecturas');
    }
};
