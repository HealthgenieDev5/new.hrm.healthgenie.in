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
            $table->boolean('is_active')->default(true)->after('night_worker_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psps', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
