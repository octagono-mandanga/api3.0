<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion.notificaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->uuid('destinatario_id');
            $table->smallInteger('tipo_id');
            $table->string('titulo', 150);
            $table->text('mensaje');
            $table->string('url_accion', 255)->nullable();
            $table->jsonb('datos_extra')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_en')->nullable();
            $table->boolean('enviada_push')->default(false);
            $table->boolean('enviada_email')->default(false);
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('destinatario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->foreign('tipo_id')->references('id')->on('notificacion.tipos_notificacion');

            $table->index(['destinatario_id', 'leida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion.notificaciones');
    }
};
