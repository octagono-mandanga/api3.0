<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.grados_institucion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('grado_id');
            $table->string('alias', 50)->nullable(); // nombre personalizado
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('grado_id')->references('id')->on('core.grados');
            $table->unique(['institucion_id', 'grado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.grados_institucion');
    }
};
