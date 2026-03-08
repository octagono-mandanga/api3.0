<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observador.tipos_observacion', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('valoracion', 20); // positiva, negativa, neutral
            $table->string('color', 10)->nullable();
            $table->string('icono', 30)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observador.tipos_observacion');
    }
};
