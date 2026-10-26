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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('in_time');
            $table->time('out_time');
            $table->enum('week_off', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->default('Sunday');
            $table->time('grace_minutes');
            $table->enum('shift_type', ['day', 'night']);
            $table->enum('sub_type', ['regular', 'reduced']);
            $table->unsignedBigInteger('sister_shift')->nullable();
            $table->timestamps();

            $table->foreign('sister_shift')->references('id')->on('shifts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
