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
            $table->string('email', 100)->unique()->nullable();
            $table->timestamp('email_verificado_en')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('genero', 15)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 150)->nullable();
            $table->smallInteger('municipio_id')->nullable();
            $table->smallInteger('etnia_id')->nullable();
            $table->smallInteger('discapacidad_id')->nullable();
            $table->smallInteger('eps_id')->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->string('estado', 15)->default('activo');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('tipo_documento_id')->references('id')->on('ref.tipos_documento');
            $table->foreign('municipio_id')->references('id')->on('ref.municipios');
            $table->foreign('etnia_id')->references('id')->on('ref.etnias');
            $table->foreign('discapacidad_id')->references('id')->on('ref.discapacidades');
            $table->foreign('eps_id')->references('id')->on('ref.eps');
            $table->unique('numero_documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.usuarios');
    }
};
