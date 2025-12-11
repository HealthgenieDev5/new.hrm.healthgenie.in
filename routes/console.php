<?php

use App\Console\Commands\FetchAttendance;
use App\Console\Commands\ProcessAttendance;
use App\Console\Commands\ProcessTenureBonuses;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

#Fetch Attendance from API every 3 minutes
Schedule::command(FetchAttendance::class)
    ->everyFifteenMinutes()
    ->runInBackground()
    ->onFailure(function () {
        Log::warning('Schedule FetchAttendance was attempted but skipped.');
    });

#Process Attendance every 30 minutes
Schedule::command(ProcessAttendance::class)
    ->everyThirtyMinutes()
    ->runInBackground()
    ->onFailure(function () {
        Log::warning('Schedule ProcessAttendance was attempted but skipped.');
    });

    # Process Tenure Bonuses every Day at 1:00 AM
Schedule::command(ProcessTenureBonuses::class)
    ->dailyAt('01:00')
    ->runInBackground()
    ->onFailure(function () {
        Log::warning('Schedule ProcessTenureBonuses was attempted but skipped.');
    });
