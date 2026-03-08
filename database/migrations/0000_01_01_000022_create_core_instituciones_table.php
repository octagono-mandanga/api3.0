<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.instituciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->smallInteger('plan_id')->nullable();
            $table->smallInteger('tema_id')->nullable();
            $table->smallInteger('municipio_id')->nullable();
            $table->string('nit', 20)->unique();
            $table->string('codigo_dane', 20)->nullable();
            $table->string('tipo_institucion', 50)->nullable(); // IE, Centro Educativo, etc.
            $table->string('nombre_legal', 150);
            $table->string('nombre_corto', 80)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email_oficial', 100)->nullable();
            $table->string('sitio_web', 150)->nullable();
            $table->string('dominio', 100)->nullable();
            $table->string('logo_url', 255)->nullable();
            $table->string('portada_url', 255)->nullable();
            $table->uuid('rector_id')->nullable();
            $table->jsonb('colores_marca')->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('core.planes');
            $table->foreign('tema_id')->references('id')->on('core.temas');
            $table->foreign('municipio_id')->references('id')->on('ref.municipios');
            $table->foreign('rector_id')->references('id')->on('auth.usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.instituciones');
    }
};
