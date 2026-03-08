<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.jornadas', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 30);
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.jornadas');
    }
};
