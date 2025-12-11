<?php

use App\Http\Controllers\TenureBonusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:Process Tenure Bonus'])->prefix('tenure-bonus')->name('tenure-bonus.')->group(function () {
    // Route::get('/', [TenureBonusController::class, 'indexBkp'])->name('index-bkp');
    Route::get('/', [TenureBonusController::class, 'index'])->name('index');
    // Route::post('/process', [TenureBonusController::class, 'processBonuses'])->name('process');
    Route::post('/{bonus}/mark-as-paid', [TenureBonusController::class, 'markAsPaid'])->name('mark-paid');
    Route::post('/{bonus}/mark-unpaid', [TenureBonusController::class, 'markUnpaid'])->name('mark-unpaid');
});
