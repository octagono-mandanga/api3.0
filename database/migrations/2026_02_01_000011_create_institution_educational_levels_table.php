<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.institution_educational_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->constrained('auth.institutions')->onDelete('cascade');
            $table->foreignUuid('educational_level_id')->constrained('core.educational_levels')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institution_id', 'educational_level_id'], 'inst_level_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.institution_educational_levels');
    }
};
