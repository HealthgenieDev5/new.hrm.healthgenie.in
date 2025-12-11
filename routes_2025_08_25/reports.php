<?php

use App\Http\Controllers\Reports\AttendanceReport;
use App\Http\Controllers\Reports\ProcessedSalaryController;
use App\Http\Controllers\TenureBonusController;
use Illuminate\Support\Facades\Route;



Route::prefix('reports')->name('reports.')->group(function () {
    Route::controller(AttendanceReport::class)->group(function () {
        Route::prefix('attendance-report')->name('attendance-report.')->group(function () {
            # Attendance Report Index page
            Route::get('/', 'index')->name('index')->middleware(['auth', 'verified', 'permission:Download Attendance']);
            # Print PDF Report
            Route::get('/generate-pdf', 'generatePDF')->name('generate-pdf')->middleware(['auth', 'verified', 'permission:View Attendance']);
        });
    });


    // Salary Reports
    // Route::resource('salary', ProcessedSalaryController::class)->middleware(['auth', 'verified', 'permission:View Salary Report']);
    Route::prefix('salary')->name('salary.')->controller(ProcessedSalaryController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware(['permission:View Salary Report']);
        Route::get('/get-filtered-workers', 'getFilteredWorkers')->name('get-filtered-workers')->middleware(['permission:View Salary Report']);
        Route::get('/incentives', 'incentives')->name('incentives')->middleware(['permission:View Salary Report']);
    });

    // Tenure Bonus
    Route::prefix('tenure-bonus')
        ->name('tenure-bonus.')
        ->controller(TenureBonusController::class)
        // ->middleware(['auth', 'verified', 'permission:View Tenure Bonus'])
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{bonus}/mark-paid', [TenureBonusController::class, 'markAsPaid'])->name('mark-paid');
            Route::post('/mark-as-paid-bulk', [TenureBonusController::class, 'markAsPaidBulk'])->name('mark-paid-bulk');
            Route::post('/{bonus}/mark-unpaid', [TenureBonusController::class, 'markUnpaid'])->name('mark-unpaid');
            Route::post('/mark-unpaid-bulk', [TenureBonusController::class, 'markUnpaidBulk'])->name('mark-unpaid-bulk');
        });
});
