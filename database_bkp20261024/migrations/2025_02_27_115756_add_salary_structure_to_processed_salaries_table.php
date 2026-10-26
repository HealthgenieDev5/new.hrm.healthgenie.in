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
        Schema::table('processed_salaries', function (Blueprint $table) {
            // Store salary structure snapshot
            $table->json('salary_structure')->nullable()->after('net_salary');
            $table->decimal('paid_days', 5, 2)->default(0)->after('net_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processed_salaries', function (Blueprint $table) {
            $table->dropColumn('salary_structure');
            $table->dropColumn('paid_days');
        });
    }
};
