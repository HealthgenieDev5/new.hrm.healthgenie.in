<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('departments', function (Blueprint $departments_table) {
        //     $departments_table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete()->after('description');

        //     $databaseName = DB::getDatabaseName();
        //     $constraintExists = DB::select("
        //         SELECT *
        //         FROM information_schema.TABLE_CONSTRAINTS
        //         WHERE TABLE_NAME = 'departments'
        //         AND CONSTRAINT_TYPE = 'UNIQUE'
        //         AND CONSTRAINT_NAME = 'departments_name_unique'
        //         AND TABLE_SCHEMA = '$databaseName'
        //     ");
        //     if ($constraintExists) {
        //         try {
        //             $departments_table->dropUnique(['name']);
        //         } catch (\Exception $e) {
        //         }
        //     }

        //     $departments_table->unique(['division_id', 'name']);
        // });

        // Schema::table('subdepartments', function (Blueprint $subdepartments_table) {
        //     $subdepartments_table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete()->after('name');

        //     $databaseName = DB::getDatabaseName();
        //     $constraintExists = DB::select("
        //         SELECT *
        //         FROM information_schema.TABLE_CONSTRAINTS
        //         WHERE TABLE_NAME = 'subdepartments'
        //         AND CONSTRAINT_TYPE = 'UNIQUE'
        //         AND CONSTRAINT_NAME = 'subdepartments_name_unique'
        //         AND TABLE_SCHEMA = '$databaseName'
        //     ");
        //     if ($constraintExists) {
        //         try {
        //             $subdepartments_table->dropUnique(['name']);
        //         } catch (\Exception $e) {
        //         }
        //     }

        //     $subdepartments_table->unique(['department_id', 'name']);
        // });

        // Schema::table('lines', function (Blueprint $lines_table) {
        //     $lines_table->foreignId('subdepartment_id')->nullable()->constrained('subdepartments')->nullOnDelete()->after('name');

        //     $databaseName = DB::getDatabaseName();
        //     $constraintExists = DB::select("
        //         SELECT *
        //         FROM information_schema.TABLE_CONSTRAINTS
        //         WHERE TABLE_NAME = 'lines'
        //         AND CONSTRAINT_TYPE = 'UNIQUE'
        //         AND CONSTRAINT_NAME = 'lines_name_unique'
        //         AND TABLE_SCHEMA = '$databaseName'
        //     ");
        //     if ($constraintExists) {
        //         try {
        //             $lines_table->dropUnique(['name']);
        //         } catch (\Exception $e) {
        //         }
        //     }


        //     $lines_table->unique(['subdepartment_id', 'name']);
        // });

        Schema::table('processes', function (Blueprint $processes_table) {
            $processes_table->foreignId('line_id')->nullable()->constrained('lines')->nullOnDelete()->after('name');
            $databaseName = DB::getDatabaseName();
            $constraintExists = DB::select("
                SELECT *
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'processes'
                AND CONSTRAINT_TYPE = 'UNIQUE'
                AND CONSTRAINT_NAME = 'processes_name_unique'
                AND TABLE_SCHEMA = '$databaseName'
            ");
            if ($constraintExists) {
                try {
                    $processes_table->dropUnique(['name']);
                } catch (\Exception $e) {
                }
            }
            $processes_table->unique(['line_id', 'name']);
        });

        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plannings');

        // if (Schema::hasColumn('departments', 'division_id')) {
        //     Schema::table('departments', function (Blueprint $departments_table) {
        //         $departments_table->dropForeign(['division_id']);
        //         $departments_table->dropUnique(['division_id', 'name']);
        //         $departments_table->dropColumn('division_id');
        //     });
        // }

        // if (Schema::hasColumn('subdepartments', 'department_id')) {
        //     Schema::table('subdepartments', function (Blueprint $subdepartments_table) {
        //         $subdepartments_table->dropForeign(['department_id']);
        //         $subdepartments_table->dropUnique(['department_id', 'name']);
        //         $subdepartments_table->dropColumn('department_id');
        //     });
        // }

        // if (Schema::hasColumn('lines', 'subdepartment_id')) {
        //     Schema::table('lines', function (Blueprint $lines_table) {
        //         $lines_table->dropForeign(['subdepartment_id']);
        //         $lines_table->dropUnique(['subdepartment_id', 'name']);
        //         $lines_table->dropColumn('subdepartment_id');
        //     });
        // }

        if (Schema::hasColumn('processes', 'line_id')) {
            Schema::table('processes', function (Blueprint $processes_table) {
                $processes_table->dropForeign(['line_id']);
                $processes_table->dropUnique(['line_id', 'name']);
                $processes_table->dropColumn('line_id');
            });
        }
    }
};
