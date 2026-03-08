<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.grados', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->smallInteger('nivel_id');
            $table->string('nombre', 30);
            $table->string('codigo', 10)->nullable();
            $table->smallInteger('orden')->default(0);
            $table->string('estado', 15)->default('activo');
            $table->timestamps();

            $table->foreign('nivel_id')->references('id')->on('core.niveles_educativos');
            $table->unique(['nivel_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.grados');
    }
};
