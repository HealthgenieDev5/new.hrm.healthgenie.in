<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('worker_tenure_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
            $table->foreignId('tenure_bonus_rule_id')->constrained('tenure_bonus_rules');
            $table->decimal('bonus_amount', 10, 2);
            $table->date('bonus_date');
            $table->date('actual_eligibility_date')->nullable();
            $table->integer('working_days_completed');
            $table->boolean('is_completion_bonus')->default(false);
            $table->boolean('is_psp_worker')->default(false);
            // $table->boolean('is_paid')->default(false);
            $table->enum('is_paid', ['yes', 'no', 'pending'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('worker_tenure_bonuses');
    }
};
