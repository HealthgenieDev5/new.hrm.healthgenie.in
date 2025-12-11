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
        Schema::create('line_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained()->onDelete('cascade');
            $table->date('date')->nullable();
            $table->unsignedInteger('workers');
            $table->unsignedInteger('extras')->default(0);
            $table->unsignedInteger('supervisors')->default(0);
            $table->unsignedInteger('qas')->default(0);
            $table->unsignedInteger('services')->default(0);
            $table->unsignedInteger('movers')->default(0);

            $table->enum('shift_type', ['day', 'night'])->default('day');

            $table->timestamps();

            // $table->unique(['line_id', 'date']);
            $table->unique(['line_id', 'date', 'shift_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_requirements');
    }
};
