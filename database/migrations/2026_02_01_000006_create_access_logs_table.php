<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.access_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('auth.users')->onDelete('set null');
            $table->foreignUuid('institution_id')->nullable()->constrained('auth.institutions')->onDelete('set null');
            $table->string('event_type');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('os')->nullable();
            $table->string('location_city')->nullable();
            $table->integer('status_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.access_logs');
    }
};
