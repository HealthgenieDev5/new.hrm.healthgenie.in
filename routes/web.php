<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RetentionGroupController;
use App\Http\Controllers\WorkerRetentionController;
use Illuminate\Support\Facades\Route;




Route::get('/', function () {
    return redirect('/dashboard/index');
});
Route::get('/dashboard', function () {
    return redirect('/dashboard/index');
})->name('dashboard');

// Worker Retention Report
Route::get('/dashboard/retention', [WorkerRetentionController::class, 'index'])->middleware(['auth', 'verified'])
    ->name('dashboard.retention');

Route::get('/dashboard/index', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard.index');

Route::get('/dashboard/attendance-summary', [DashboardController::class, 'getAttendanceSummary'])->middleware(['auth', 'verified'])->name('dashboard.attendance-summary');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::prefix('ajax')->name('ajax.')->group(function () {
    // Route::get('/workers/filter', [WorkerController::class, 'filter'])->name('workers.filter');
});


Route::resource('retention-groups', RetentionGroupController::class)->middleware(['auth', 'verified', 'permission:Manage Retention Groups']);


require __DIR__ . '/auth.php';
require __DIR__ . '/attendance.php';
require __DIR__ . '/overrides.php';
require __DIR__ . '/master.php';
require __DIR__ . '/reports.php';
require __DIR__ . '/settings.php';
require __DIR__ . '/worker.php';
require __DIR__ . '/planning.php';
