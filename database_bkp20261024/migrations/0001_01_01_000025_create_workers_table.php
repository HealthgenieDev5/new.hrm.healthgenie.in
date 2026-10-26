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
        Schema::create('workers', function (Blueprint $table) {

            $table->id(); // Primary key
            $table->string('code')->unique(); // Unique worker code
            $table->unsignedBigInteger('psp_id')->nullable(); // Foreign key for PSP
            $table->string('photo')->nullable(); // Worker photo
            $table->enum('employment_status', ['Active', 'Left', 'Terminated', 'Absconded', 'Retired', 'Under Resignation'])->default('Active'); // Employment Status with default
            $table->string('name'); // Worker name
            $table->unsignedBigInteger('designation_id')->nullable(); // Foreign key for Designation
            $table->unsignedBigInteger('workertype_id')->nullable(); // Foreign key for Worker Type
            $table->enum('gender', ['male', 'female', 'other']); // Gender
            $table->unsignedBigInteger('shift_id')->nullable(); // Foreign key for Shift
            // $table->unsignedBigInteger('shift_id_2')->nullable(); // Foreign key for Shift
            $table->unsignedBigInteger('process_id')->nullable(); // Foreign key for Process
            $table->unsignedBigInteger('level_id')->nullable(); // Foreign key for Level
            $table->string('father_name')->nullable(); // Father's name
            $table->string('husband_name')->nullable(); // Husband's name (if applicable)
            $table->date('date_of_birth')->nullable(); // Date of birth

            $table->enum('religion', ['Hindu', 'Muslim', 'Christian', 'Sikh', 'Jain', 'Budhist']); // Religion

            $table->date('date_of_joining')->nullable(); // Date of joining
            $table->string('mobile_number', 10)->nullable(); // Mobile number
            $table->boolean('uan')->default(false); // UAN availability (yes/no)
            $table->string('uan_number')->nullable(); // UAN number
            $table->boolean('esic')->default(false); // ESIC availability (yes/no)
            $table->string('esic_number', 50)->nullable()->unique(); // ESI Number (nullable)
            $table->enum('marital_status', ['Unmarried', 'Married', 'Divorced', 'Widowed']); // Marital Status
            $table->text('permanent_address')->nullable(); // Permanent address
            $table->string('permanent_pincode', 10)->nullable(); // Permanent pincode
            $table->string('permanent_district')->nullable(); // Permanent district
            $table->string('permanent_state')->nullable(); // Permanent state
            $table->text('present_address')->nullable(); // Present address
            $table->string('present_pincode', 10)->nullable(); // Present pincode
            $table->string('present_district')->nullable(); // Present district
            $table->string('present_state')->nullable(); // Present state
            $table->string('adhar_number', 50)->nullable(); // Aadhaar number
            $table->string('adhar_front')->nullable(); // Aadhaar front photo
            $table->string('adhar_back')->nullable(); // Aadhaar back photo
            $table->string('uan_photo')->nullable(); // UAN photo
            $table->string('form_11_photo')->nullable(); // Form-11 photo
            $table->string('affidavit_photo')->nullable(); // Affidavit photo
            $table->string('lop_photo')->nullable(); // LOP photo

            $table->unsignedBigInteger('created_by')->nullable();

            $table->softDeletes(); // Adds the 'deleted_at' column
            $table->unsignedBigInteger('deleted_by')->nullable();


            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraints
            $table->foreign('psp_id')->references('id')->on('psps')->onDelete('set null');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
            $table->foreign('workertype_id')->references('id')->on('workertypes')->onDelete('set null');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            // $table->foreign('shift_id_2')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('set null');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
