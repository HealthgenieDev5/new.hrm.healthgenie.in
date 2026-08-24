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
        Schema::create('call_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('phone_number', 50);
            $table->string('call_type', 50);
            $table->unsignedInteger('duration');
            $table->timestamp('call_timestamp');
            $table->timestamps();

            $table->unique(
                ['device_id', 'phone_number', 'call_type', 'duration', 'call_timestamp'],
                'call_records_device_call_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_records');
    }
};
