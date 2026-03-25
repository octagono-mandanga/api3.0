<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core.instituciones', function (Blueprint $table) {
            $table->dropForeign(['tema_id']);
            $table->dropColumn('tema_id');
        });
    }

    public function down(): void
    {
        Schema::table('core.instituciones', function (Blueprint $table) {
            $table->smallInteger('tema_id')->nullable()->after('plan_id');
            $table->foreign('tema_id')->references('id')->on('core.temas');
        });
    }
};
