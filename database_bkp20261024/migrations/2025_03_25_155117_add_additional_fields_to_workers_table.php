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
        Schema::table('workers', function (Blueprint $table) {
            $table->date('last_working__absconded_date')->nullable()->after('line_id');
            $table->unsignedBigInteger('notice_period_loa')->nullable()->after('last_working__absconded_date');
            $table->float('notice_period_served')->nullable()->after('notice_period_loa');
            $table->string('permanent_city')->nullable()->after('permanent_state');
            $table->string('present_city')->nullable()->after('present_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('last_working__absconded_date');
            $table->dropColumn('notice_period_loa');
            $table->dropColumn('notice_period_served');
            $table->dropColumn('permanent_city');
            $table->dropColumn('present_city');
        });
    }
};
