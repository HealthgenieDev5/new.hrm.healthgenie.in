<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Worker;
use App\Services\AttendanceProcessingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProcessSingleWorkerAttendance extends Command
{
    protected $signature = 'attendance:process-worker {code} {--month=}';
    protected $description = 'Process attendance for a single worker';

    public function handle()
    {
        $code = $this->argument('code');
        $month = $this->option('month');

        $worker = Worker::where('code', $code)->first();

        if (!$worker) {
            $this->error("Worker not found for code: $code");
            return 1;
        }

        // Get the date range based on month option
        $monthDate = $month ? Carbon::parse($month . '-01') : Carbon::now();
        $startDate = $monthDate->copy()->startOfMonth();
        $endDate = $monthDate->isCurrentMonth() ? Carbon::now() : $monthDate->copy()->endOfMonth();
        $dateRange = CarbonPeriod::create($startDate, $endDate);

        // Setup the attendance processing service
        $service = new AttendanceProcessingService();
        $service->setDateRange($dateRange);
        $service->setWorkers(collect([$worker]));

        // Process attendance
        $service->processAttendance();

        $this->info("✅ Attendance processed for worker: $code");
        return 0;
    }
}
