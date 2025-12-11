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
        Schema::create('processed_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id'); // Foreign key to workers table
            $table->date('date'); // Attendance date

            $table->time('shift_start')->nullable(); // Actual in-time
            $table->time('shift_end')->nullable(); // Actual in-time
            $table->string('shift_type', 10)->nullable(); // Shift type

            $table->time('in_time')->nullable(); // Actual in-time
            $table->time('out_time')->nullable(); // Actual out-time
            $table->time('in_time_processed')->nullable(); // Adjusted in-time
            $table->time('out_time_processed')->nullable(); // Adjusted out-time
            $table->float('late_coming_minutes')->default(0); // Late minutes
            $table->float('early_going_minutes')->default(0); // Early minutes
            $table->boolean('is_missed_punch')->default(false); // Whether a punch is missing
            $table->boolean('is_absent')->default(false); // Whether absent
            $table->boolean('is_holiday')->default(false); // Whether absent
            $table->boolean('is_weekoff')->default(false); // Whether absent

            $table->float('shift_hours')->default(0); // Shift hours
            $table->float('total_work_hours')->default(0); // Total work hours
            $table->float('work_hours_within_shift')->default(0); // Work hours within shift
            $table->float('grace_minutes')->default(0); // Grace minutes

            $table->float('adjustable_minutes')->default(0); // Non-working minutes
            $table->float('adjusted_minutes')->default(0); // Adjusted minutes
            $table->string('status', 50)->nullable();
            $table->text('status_remarks')->nullable();

            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade'); // Foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_attendance');
    }
};
