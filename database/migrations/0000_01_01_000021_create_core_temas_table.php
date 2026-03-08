<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.temas', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('color_primario', 10)->nullable();
            $table->string('color_secundario', 10)->nullable();
            $table->string('color_terciario', 10)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.temas');
    }
};
