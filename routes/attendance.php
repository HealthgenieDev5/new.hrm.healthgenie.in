<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;



Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::controller(AttendanceController::class)->group(function () {

        # Attendance Index
        Route::get('/', 'index')->name('index')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Get Workers based on PSP Filter
        Route::get('/get-filtered-workers', 'GetFilterdWorkers')->name('get-filtered-workers')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Get Raw attendance for Worker on Manual Punching Page
        Route::get('/get-raw-data', 'GetRawData')->name('get-raw-data')->middleware(['auth', 'verified', 'permission:Manual Punching']);

        # Get Attendance based on all filter
        Route::post('get-attendance', 'getAttendance')->name('get-attendance')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Missed Punch Report Index page
        Route::get('/missed-punch', 'missedPunch')->name('missed-punch')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Get Attendance Summary based on all filter
        Route::post('get-missed-punch-datatable', 'getMissedPunchDatatable')->name('get-missed-punch-datatable')->middleware(['auth', 'verified', 'permission:View Attendance']);


        # Wrong Shift Report Index page
        Route::get('/wrong-shift', 'wrongShift')->name('wrong-shift')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Get Wrong shifts based on all filter
        Route::post('get-wrong-shift-datatable', 'getWrongShiftDatatable')->name('get-wrong-shift-datatable')->middleware(['auth', 'verified', 'permission:View Attendance']);


        # Attendance Summary
        Route::get('/summary', 'summary')->name('summary')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Get Attendance Summary based on all filter
        Route::post('get-sumamry', 'getAttendanceSummary')->name('get-sumamry')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Continuous Absent
        Route::get('/continuous-absent', 'ContinuousAbsent')->name('continuous-absent')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Get Continuous Absent based on filter
        Route::post('get-continuous-absent', 'getContinuousAbsent')->name('get-continuous-absent')->middleware(['auth', 'verified', 'permission:View Attendance']);

        # Re process Attendance
        Route::get('/recalculate', 'reCalculate')->name('recalculate')->middleware(['auth', 'verified', 'permission:Recalculate Attendance']);
        Route::get('/refresh', 'refreshAttendance')->name('refresh')->middleware(['auth', 'verified', 'permission:Recalculate Attendance']);
    });
});
