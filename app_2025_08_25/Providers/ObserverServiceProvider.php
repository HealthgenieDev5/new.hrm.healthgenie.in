<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Level;
use App\Models\Line;
use App\Models\Process;
use App\Models\ProductionProduct;
use App\Models\Psp;
use App\Models\Shift;
use App\Models\Subdepartment;
use App\Models\Workertype;
use App\Models\WorkingArea;
use App\Observers\AttendanceFilterObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    // 'divisions' => Division::with('departments.subdepartments.lines.processes')->get(),
    public function boot(): void
    {
        Psp::observe(AttendanceFilterObserver::class);
        Shift::observe(AttendanceFilterObserver::class);
        Workertype::observe(AttendanceFilterObserver::class);
        WorkingArea::observe(AttendanceFilterObserver::class);
        Designation::observe(AttendanceFilterObserver::class);
        ProductionProduct::observe(AttendanceFilterObserver::class);
        Level::observe(AttendanceFilterObserver::class);
        Division::observe(AttendanceFilterObserver::class);
        Department::observe(AttendanceFilterObserver::class);
        Subdepartment::observe(AttendanceFilterObserver::class);
        Line::observe(AttendanceFilterObserver::class);
        Process::observe(AttendanceFilterObserver::class);
    }
}
