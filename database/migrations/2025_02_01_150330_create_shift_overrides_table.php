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
        Schema::create('shift_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id'); // Foreign key to workers table
            $table->unsignedBigInteger('shift_id')->nullable(); // Foreign key to shifts table
            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes(); // Adds the 'deleted_at' column
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade'); // Foreign key
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null'); // Foreign key
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_overrides');
    }
};
