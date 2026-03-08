<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion.preferencias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->smallInteger('tipo_id');
            $table->boolean('push_habilitado')->default(true);
            $table->boolean('email_habilitado')->default(true);
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->foreign('tipo_id')->references('id')->on('notificacion.tipos_notificacion');
            $table->unique(['usuario_id', 'tipo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion.preferencias');
    }
};
