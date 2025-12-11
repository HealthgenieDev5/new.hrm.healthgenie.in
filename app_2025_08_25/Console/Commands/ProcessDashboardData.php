<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessDashboardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:dashboard
                            {--month= : Year and Month for processing attendance (YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process dashboard data every hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::put('process_dashboard_data_running', true, now()->addMinutes(15));

        $monthOption = $this->option('month') ?? date('Y-m');

        try {

            // $this->salaryProcessingServiceInterface->processSalaries($workerCodes, $year, $month);

            Log::info('ProcessSalariesCommand Completed at: ' . now());
            $this->info('ProcessSalariesCommand Completed at: ' . now());
        } catch (\Exception $e) {
            Log::error('ProcessSalariesCommand failed at: ' . now() . $e->getMessage());
            $this->error('ProcessSalariesCommand failed at: ' . now() . $e->getMessage());
        } finally {
            Log::info('ProcessSalariesCommand finished at: ' . now());
            $this->info('ProcessSalariesCommand finished at: ' . now());
            Cache::forget('process_dashboard_data_running');
        }
    }
}
