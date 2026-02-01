<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth.institutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nit')->unique();
            $table->string('legal_name');
            $table->string('short_name');
            $table->string('dane_code')->nullable();
            $table->string('institution_type')->nullable();
            $table->string('official_email')->nullable();
            $table->string('website_url')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('status')->default('active');
            $table->foreignUuid('rector_id')->nullable()->constrained('auth.users')->onDelete('set null');
            $table->jsonb('branding_colors')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth.institutions');
    }
};
