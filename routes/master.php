<?php

use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\DesignationController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\HolidayController;
use App\Http\Controllers\Master\LevelController;
use App\Http\Controllers\Master\LineController;
use App\Http\Controllers\Master\ProcessController;
use App\Http\Controllers\Master\ProductionProductController;
use App\Http\Controllers\Master\PspController;
use App\Http\Controllers\Master\ShiftController;
use App\Http\Controllers\Master\SubdepartmentController;
use App\Http\Controllers\Master\WorkertypeController;
use App\Http\Controllers\Master\SalaryMasterController;
use App\Http\Controllers\Master\WorkingAreaController;
use App\Http\Controllers\WorkerRetentionController;
use Illuminate\Support\Facades\Route;



require __DIR__ . '/worker.php';
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('divisions', DivisionController::class)->middleware(['auth', 'verified', 'permission:Manage Divisions']);
    Route::resource('departments', DepartmentController::class)->middleware(['auth', 'verified', 'permission:Manage Departments']);
    Route::resource('subdepartments', SubdepartmentController::class)->middleware(['auth', 'verified', 'permission:Manage Sub Departments']);
    Route::resource('lines', LineController::class)->middleware(['auth', 'verified', 'permission:Manage Lines']);
    Route::resource('processes', ProcessController::class)->middleware(['auth', 'verified', 'permission:Manage Processes']);
    Route::resource('levels', LevelController::class)->middleware(['auth', 'verified', 'permission:Manage Levels']);
    Route::resource('workertypes', WorkertypeController::class)->middleware(['auth', 'verified', 'permission:Manage Workertypes']);
    Route::resource('shifts', ShiftController::class)->middleware(['auth', 'verified', 'permission:Manage Shifts']);
    Route::resource('designations', DesignationController::class)->middleware(['auth', 'verified', 'permission:Manage Designations']);
    Route::resource('holidays', HolidayController::class)->middleware(['auth', 'verified', 'permission:Manage Holidays']);
    Route::resource('psps', PspController::class)->middleware(['auth', 'verified', 'permission:Manage PSP']);
    Route::patch('psps/{psp}/toggle-status', [PspController::class, 'toggleStatus'])->name('psps.toggle-status')->middleware(['auth', 'verified', 'permission:Manage PSP']);





    Route::resource('salary-master', SalaryMasterController::class)->middleware(['auth', 'verified', 'permission:Manage Salary Master']);
    Route::resource('working-area', WorkingAreaController::class)->middleware(['auth', 'verified', 'permission:Manage Working Area']);
    Route::resource('production-product', ProductionProductController::class)->middleware(['auth', 'verified', 'permission:Manage Working Area']);
});
