<?php

namespace App\Providers;

use App\Interfaces\ProductionPlanningInterface;
use App\Interfaces\SalaryProcessingServiceInterface;
use App\Interfaces\WorkerAttendanceRepositoryInterface;
use App\Interfaces\WorkerServiceInterface;
use App\Repositories\WorkerAttendanceRepository;
use App\Services\ProductionPlanningService;
use App\Services\SalaryProcessingService;
use App\Services\WorkerService;
use Illuminate\Support\ServiceProvider;

class InterfacesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(WorkerServiceInterface::class, WorkerService::class);

        $this->app->bind(SalaryProcessingServiceInterface::class, SalaryProcessingService::class);

        $this->app->bind(WorkerAttendanceRepositoryInterface::class, WorkerAttendanceRepository::class);

        $this->app->bind(ProductionPlanningInterface::class, ProductionPlanningService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
