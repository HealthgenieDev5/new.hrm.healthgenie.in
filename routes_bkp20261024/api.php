<?php

use App\Http\Controllers\Api\CallLogBatchController;
use App\Http\Middleware\LogNazrulApiRequests;
use Illuminate\Support\Facades\Route;

Route::middleware(LogNazrulApiRequests::class)->group(function () {
    Route::post('/call-logs/batch', CallLogBatchController::class);
});
