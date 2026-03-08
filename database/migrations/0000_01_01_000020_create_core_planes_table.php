<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.planes', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('codigo', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('max_usuarios')->nullable();
            $table->integer('max_estudiantes')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.planes');
    }
};
