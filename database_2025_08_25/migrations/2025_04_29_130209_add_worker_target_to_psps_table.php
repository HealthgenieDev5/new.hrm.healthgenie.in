<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('psps', function (Blueprint $table) {
            $table->integer('day_worker_target')->default(0);
            $table->integer('night_worker_target')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psps', function (Blueprint $table) {
            $table->dropColumn(['day_worker_target', 'night_worker_target']);
        });
    }
};
