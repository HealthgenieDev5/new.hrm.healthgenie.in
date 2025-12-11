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
        Schema::table('workers', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('lop_photo');
            $table->unsignedBigInteger('department_id')->nullable()->after('division_id');
            $table->unsignedBigInteger('subdepartment_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('working_area_id')->nullable()->after('subdepartment_id');
            $table->unsignedBigInteger('production_product_id')->nullable()->after('working_area_id');
            $table->unsignedBigInteger('line_id')->nullable()->after('production_product_id');

            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('subdepartment_id')->references('id')->on('subdepartments')->onDelete('set null');
            $table->foreign('working_area_id')->references('id')->on('working_areas')->onDelete('set null');
            $table->foreign('production_product_id')->references('id')->on('production_products')->onDelete('set null');
            $table->foreign('line_id')->references('id')->on('lines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['subdepartment_id']);
            $table->dropForeign(['working_area_id']);
            $table->dropForeign(['production_product_id']);
            $table->dropForeign(['line_id']);

            $table->dropColumn('division_id');
            $table->dropColumn('department_id');
            $table->dropColumn('subdepartment_id');
            $table->dropColumn('working_area_id');
            $table->dropColumn('production_product_id');
            $table->dropColumn('line_id');
        });
    }
};
