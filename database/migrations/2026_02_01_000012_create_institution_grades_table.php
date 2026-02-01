<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.institution_grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->constrained('auth.institutions')->onDelete('cascade');
            $table->foreignUuid('grade_id')->constrained('core.grades')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institution_id', 'grade_id'], 'inst_grade_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.institution_grades');
    }
};
