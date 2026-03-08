<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion.tipos_notificacion', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('codigo', 30);
            $table->string('icono', 30)->nullable();
            $table->boolean('permite_push')->default(true);
            $table->boolean('permite_email')->default(true);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->unique('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion.tipos_notificacion');
    }
};
