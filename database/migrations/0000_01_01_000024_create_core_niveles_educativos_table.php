<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.niveles_educativos', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('codigo', 10)->nullable();
            $table->smallInteger('orden')->default(0);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.niveles_educativos');
    }
};
