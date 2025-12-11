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
        Schema::create('revisions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('revisionable_type'); // Model class name
            $table->unsignedBigInteger('revisionable_id'); // Model ID
            $table->json('old_values')->nullable(); // Old values
            $table->json('new_values')->nullable(); // New values
            $table->unsignedBigInteger('user_id')->nullable(); // User who made the change
            $table->timestamp('created_at')->useCurrent(); // Timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
