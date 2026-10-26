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
        Schema::create('retention_groups', function (Blueprint $table) {
            $table->id();
            // 'active' or 'left'
            $table->enum('type', ['active', 'left'])->default('active');
            $table->string('label');
            $table->integer('min')->default(0);
            $table->integer('max')->default(0);
            $table->integer('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retention_groups');
    }
};
