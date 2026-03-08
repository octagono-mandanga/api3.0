<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.usuarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->smallInteger('tipo_documento_id')->nullable();
            $table->string('numero_documento', 25)->nullable();
            $table->string('primer_nombre', 50);
            $table->string('segundo_nombre', 50)->nullable();
            $table->string('primer_apellido', 50);
            $table->string('segundo_apellido', 50)->nullable();
            $table->string('email', 100)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('genero', 15)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('password_hash', 255);
            $table->string('avatar_url', 255)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('tipo_documento_id')->references('id')->on('ref.tipos_documento');
            $table->unique('numero_documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.usuarios');
    }
};
