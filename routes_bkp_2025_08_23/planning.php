<?php

use App\Http\Controllers\Planning\PlanningController;
use App\Http\Controllers\Planning\TemporaryLineAssignmentController;
use App\Http\Controllers\Reports\AttendanceReport;
use App\Http\Controllers\Reports\ProcessedSalaryController;
use Illuminate\Support\Facades\Route;



Route::prefix('production')->name('production.')->group(function () {

    Route::prefix('planning')->name('planning.')->group(function () {
        Route::post('assign-to-line', [PlanningController::class, 'assignToLine'])
            ->name('assign-to-line')
            ->middleware(['auth', 'verified', 'permission:Manage Planning']);


        Route::post('remove-from-line', [PlanningController::class, 'removeFromLine'])
            ->name('remove-from-line')
            ->middleware(['auth', 'verified', 'permission:Manage Planning']);

        Route::get('export-assigned-workers', [PlanningController::class, 'exportAssignedWorkers'])
            ->name('export-assigned-workers')
            ->middleware(['auth', 'verified', 'permission:Manage Planning']);

        Route::post('close-shift', [PlanningController::class, 'closeShift'])
            ->name('close-shift')
            ->middleware(['auth', 'verified', 'permission:Manage Planning']);
    });

    // Route::get('lines/{line}/processes', [PlanningController::class, 'getProcessesForLine']);

    Route::prefix('temporary-line-assignment')->name('temporary-line-assignment.')->controller(TemporaryLineAssignmentController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware(['auth', 'verified', 'permission:Manage Planning']);
        Route::post('/datatable', 'getDatatable')->name('get-datatable')->middleware(['auth', 'verified', 'permission:Manage Planning']);
    });

    Route::resource('planning', PlanningController::class)->middleware(['auth', 'verified', 'permission:Manage Planning']);
});
