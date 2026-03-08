<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academico.unidades_tematicas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asignatura_id');
            $table->smallInteger('grado_id')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->smallInteger('orden')->default(0);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('asignatura_id')->references('id')->on('academico.asignaturas')->onDelete('cascade');
            $table->foreign('grado_id')->references('id')->on('core.grados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academico.unidades_tematicas');
    }
};
