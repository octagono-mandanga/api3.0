<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.institution_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->constrained('auth.institutions')->onDelete('cascade');
            $table->foreignUuid('role_id')->constrained('auth.roles')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institution_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.institution_roles');
    }
};
