<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.identidades_externas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('usuario_id');
            $table->string('proveedor', 30); // google, microsoft, facebook
            $table->string('proveedor_user_id', 100);
            $table->string('proveedor_email', 100)->nullable();
            $table->timestamp('vinculado_en')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('auth.usuarios')->onDelete('cascade');
            $table->unique(['usuario_id', 'proveedor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.identidades_externas');
    }
};
