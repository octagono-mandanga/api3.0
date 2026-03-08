<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.temas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unidad_id');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->smallInteger('orden')->default(0);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('unidad_id')->references('id')->on('academico.unidades_tematicas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.temas');
    }
};
