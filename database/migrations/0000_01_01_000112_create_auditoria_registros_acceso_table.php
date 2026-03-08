<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria.registros_acceso', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id')->nullable();
            $table->string('tipo', 20); // login, logout, login_fallido, password_reset
            $table->string('metodo', 30)->nullable(); // password, google, azure
            $table->boolean('exitoso')->default(true);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('motivo_fallo', 100)->nullable();
            $table->timestamp('created_at');

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('set null');
            $table->index(['usuario_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria.registros_acceso');
    }
};
