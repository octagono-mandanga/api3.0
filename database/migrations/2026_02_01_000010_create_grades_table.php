<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('educational_level_id')->constrained('core.educational_levels')->onDelete('cascade');
            $table->string('short_name');
            $table->string('full_name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.grades');
    }
};
