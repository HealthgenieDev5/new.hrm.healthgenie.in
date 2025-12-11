<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenure_bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('working_days_start');
            $table->integer('working_days_end');
            $table->decimal('bonus_amount', 10, 2);
            $table->boolean('is_completion_bonus')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenure_bonus_rules');
    }
};
