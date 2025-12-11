<?php

use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

Route::controller(WorkerController::class)->group(function () {
    Route::prefix('workers')->name('workers.')->group(function () {
        #Apply middleware for the 'index' method
        Route::get('/', 'index')->name('index')->middleware(['auth', 'verified', 'permission:View Workers']);
        Route::post('/get-workers', 'getworkers')->name('getworkers')->middleware(['auth', 'verified', 'permission:View Workers']);

        #Apply middleware for the 'create' method
        Route::get('/create', 'create')->name('create')->middleware(['auth', 'verified', 'permission:Create Worker']);

        #Apply middleware for the 'store' method
        Route::post('/', 'store')->name('store')->middleware(['auth', 'verified', 'permission:Create Worker']);

        #Apply middleware for the 'edit' and 'update' methods
        Route::get('/{worker}/edit', 'edit')->name('edit')->middleware(['auth', 'verified', 'permission:Edit Worker']);
        Route::put('/{worker}', 'update')->name('update')->middleware(['auth', 'verified', 'permission:Edit Worker']);

        #Apply middleware for the 'destroy' method
        Route::delete('/{worker}', 'destroy')->name('destroy')->middleware(['auth', 'verified', 'permission:Delete Worker']);

        Route::get('/deleted', 'deleted')->name('deleted')->middleware(['auth', 'verified', 'permission:Show Deleted Workers']);
        Route::post('/deleted/{id}/restore', 'restore')->name('restore')->middleware(['auth', 'verified', 'permission:Restore Deleted Workers']);

        Route::get('/{worker}', 'show')->name('show')->middleware(['auth', 'verified', 'permission:View Workers']);

        #Long Form
        Route::get('/long-form/{worker}/edit', 'editLongForm')->name('edit-long-form')->middleware(['auth', 'verified', 'permission:Edit Worker Long Form']);
        Route::put('/long-form/{worker}', 'updateLongForm')->name('update-long-form')->middleware(['auth', 'verified', 'permission:Edit Worker Long Form']);
    });
});













Route::prefix('import')->name('import.')->group(function () {
    Route::get('/workers', [WorkerController::class, 'showImportForm'])->name('workers');
    Route::post('/workers/submit', [WorkerController::class, 'import'])->name('workers.submit');
});
