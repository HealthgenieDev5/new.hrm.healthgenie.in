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
        Schema::create('temporary_line_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
            $table->foreignId('line_id')->constrained('lines')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained()->onDelete('cascade'); // Shift assigned during temporary assignment
            $table->date('start_date');  // Assignment begins
            $table->date('end_date');    // Assignment ends
            $table->foreignId('process_id')->nullable()->constrained('processes')->onDelete('set null');
            $table->timestamps();

            // Optional: Avoid duplicate assignments for same worker, date range, and line
            $table->unique(['worker_id', 'line_id', 'start_date', 'end_date'], 'unique_temp_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_line_assignments');
    }
};
