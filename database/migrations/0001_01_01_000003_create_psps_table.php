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
        Schema::create('psps', function (Blueprint $table) {
            $table->id();

            $table->string('name'); #Psp name
            $table->string('logo')->nullable(); #Psp logo url
            $table->text('address'); #Psp Address
            $table->string('city'); #Psp city
            $table->string('state'); #Psp state
            $table->string('pincode'); #Psp pincode
            $table->string('mobile_number')->nullable(); #Psp phone_number
            $table->string('alternate_number')->nullable()->nullable(); #Psp alternate_number
            $table->string('email_id')->nullable(); #Psp alternate_number
            $table->string('contact_person')->nullable(); #Psp contact person
            $table->string('code', 5)->unique()->nullable(false);

            $table->unsignedBigInteger('user_id')->nullable(); // Add the user_id column
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psps');
    }
};
