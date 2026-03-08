<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref.tipos_documento', function (Blueprint $table) {
            $table->smallInteger('id')->autoIncrement();
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 50);
            $table->string('estado', 15)->default('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref.tipos_documento');
    }
};
