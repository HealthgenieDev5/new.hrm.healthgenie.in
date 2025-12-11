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
        Schema::create('processed_salary_incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');

            // Salary components
            $table->decimal('basic', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('conveyance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('fuel_allowance', 10, 2)->default(0);
            $table->decimal('vacation_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('gross', 10, 2)->default(0);

            // Deductions (PF & ESI)
            $table->decimal('pf_employee', 10, 2)->default(0);
            $table->decimal('pf_employer', 10, 2)->default(0);
            $table->decimal('esic_employee', 10, 2)->default(0);
            $table->decimal('esic_employer', 10, 2)->default(0);

            // Bonus & Net Salary
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->json('salary_structure')->nullable();
            $table->decimal('paid_days', 5, 2)->default(0);

            // Tracking
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // Soft delete
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_salary_incentives');
    }
};
