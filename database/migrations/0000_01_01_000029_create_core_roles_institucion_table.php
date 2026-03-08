<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.roles_institucion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institucion_id');
            $table->smallInteger('rol_id');
            $table->string('alias', 50)->nullable(); // nombre personalizado del rol en la institución
            $table->jsonb('permisos_extra')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('institucion_id')->references('id')->on('core.instituciones')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('auth.roles');
            $table->unique(['institucion_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.roles_institucion');
    }
};
