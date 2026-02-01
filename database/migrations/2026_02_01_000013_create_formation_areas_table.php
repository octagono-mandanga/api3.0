<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.formation_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('educational_level_id')->constrained('core.educational_levels')->onDelete('cascade');
            $table->string('name');
            $table->string('short_name');
            $table->string('status')->default('active'); // active, inactive
            $table->boolean('is_mandatory')->default(true); // obligatoriedad para promoción
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.formation_areas');
    }
};
