<?php

use App\Http\Controllers\Overrides\ManualPunchingController;
use App\Http\Controllers\Overrides\ShiftOverridesController;
use App\Http\Controllers\Overrides\UserRoleController;
use Illuminate\Support\Facades\Route;



Route::prefix('overrides')->name('overrides.')->group(function () {

    Route::resource('shiftoverride', ShiftOverridesController::class)->middleware(['auth', 'verified', 'permission:Manage Shift Override']);

    Route::resource('manualpunching', ManualPunchingController::class)->middleware(['auth', 'verified', 'permission:Manual Punching']);
});
