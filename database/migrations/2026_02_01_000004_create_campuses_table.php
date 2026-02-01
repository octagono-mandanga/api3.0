<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.campuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->constrained('auth.institutions')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_main')->default(false);
            $table->string('status')->default('active');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->integer('city_id')->nullable();
            $table->point('location')->nullable(); // Assuming point based on model 'location'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.campuses');
    }
};
