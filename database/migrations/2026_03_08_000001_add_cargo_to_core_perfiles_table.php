<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core.perfiles', function (Blueprint $table) {
            $table->string('cargo', 100)->nullable()->after('rol_id');
        });
    }

    public function down(): void
    {
        Schema::table('core.perfiles', function (Blueprint $table) {
            $table->dropColumn('cargo');
        });
    }
};
