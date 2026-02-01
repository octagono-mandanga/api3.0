<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.active_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('auth.users')->onDelete('cascade');
            $table->foreignUuid('institution_id')->nullable()->constrained('auth.institutions')->onDelete('cascade');
            $table->string('token_id')->index();
            $table->timestamp('last_activity')->index();
            $table->ipAddress('ip_address')->nullable();
            $table->boolean('is_online')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.active_sessions');
    }
};
