<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria.sesiones_activas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->string('token_hash', 64);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('dispositivo', 100)->nullable();
            $table->string('ubicacion', 100)->nullable();
            $table->timestamp('ultimo_acceso')->nullable();
            $table->timestamp('expira_en')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->index(['usuario_id', 'ultimo_acceso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria.sesiones_activas');
    }
};
