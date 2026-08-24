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
        Schema::create('call_record_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_record_id')->constrained('call_records')->onDelete('cascade');
            $table->string('disposition', 50);
            $table->string('sub_disposition', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('disposed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disposed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_record_dispositions');
    }
};
