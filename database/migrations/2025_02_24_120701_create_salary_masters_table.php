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
        Schema::create('salary_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->onDelete('cascade'); // Connect to Worker
            $table->decimal('basic', 10, 2);
            $table->decimal('hra', 10, 2)->nullable();
            $table->decimal('conveyance', 10, 2)->nullable();
            $table->decimal('medical_allowance', 10, 2)->nullable();
            $table->decimal('special_allowance', 10, 2)->nullable();
            $table->decimal('fuel_allowance', 10, 2)->nullable();
            $table->decimal('vacation_allowance', 10, 2)->nullable();
            $table->decimal('other_allowance', 10, 2)->nullable();
            $table->boolean('bonus_allowed')->default(0);
            $table->boolean('pf_allowed')->default(0);
            $table->boolean('esic_allowed')->default(0); // Fixed naming
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_masters');
    }
};
