<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion.dispositivos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->string('token_fcm', 255);
            $table->string('plataforma', 20); // android, ios, web
            $table->string('modelo', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamp('ultimo_uso')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->unique('token_fcm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion.dispositivos');
    }
};
