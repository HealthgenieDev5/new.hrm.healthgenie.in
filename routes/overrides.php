<?php

use App\Http\Controllers\Overrides\ManualPunchingController;
use App\Http\Controllers\Overrides\ShiftOverridesController;
use Illuminate\Support\Facades\Route;

Route::prefix('overrides')->name('overrides.')->group(function () {

    Route::get('shiftoverride/create-multiple', [ShiftOverridesController::class, 'createMultiple'])->name('shiftoverride.createMultiple')->middleware(['auth', 'verified', 'permission:Manage Shift Override']);
    Route::post('shiftoverride/store-multiple', [ShiftOverridesController::class, 'storeMultiple'])->name('shiftoverride.storeMultiple')->middleware(['auth', 'verified', 'permission:Manage Shift Override']);
    Route::post('shiftoverride/get-shift-overrides', [ShiftOverridesController::class, 'getShiftOverrides'])->name('shiftoverride.getShiftOverrides')->middleware(['auth', 'verified', 'permission:Manage Shift Override']);
    Route::resource('shiftoverride', ShiftOverridesController::class)->middleware(['auth', 'verified', 'permission:Manage Shift Override']);

    Route::resource('manualpunching', ManualPunchingController::class)->middleware(['auth', 'verified', 'permission:Manual Punching']);
});
