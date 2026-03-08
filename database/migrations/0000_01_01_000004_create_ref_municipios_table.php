<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref.municipios', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('nombre', 50);
            $table->string('codigo', 10)->nullable();
            $table->smallInteger('departamento_id');
            $table->string('estado', 15)->default('activo');

            $table->foreign('departamento_id')->references('id')->on('ref.departamentos');
            $table->unique(['nombre', 'departamento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref.municipios');
    }
};
