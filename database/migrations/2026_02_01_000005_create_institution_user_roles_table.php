<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.institution_user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('auth.users')->onDelete('cascade');
            $table->foreignUuid('institution_id')->nullable()->constrained('auth.institutions')->onDelete('cascade');
            $table->foreignUuid('role_id')->constrained('auth.roles')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.institution_user_roles');
    }
};
