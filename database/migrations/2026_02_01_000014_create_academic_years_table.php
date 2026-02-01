<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core.academic_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('educational_level_id')->constrained('core.educational_levels')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status'); // active, previous, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.academic_years');
    }
};
