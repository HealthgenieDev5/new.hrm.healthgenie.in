<?php

namespace App\Console\Commands;

use App\Interfaces\SalaryProcessingServiceInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessSalariesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:process
                            {--month= : Year and Month for processing attendance (YYYY-MM)}
                            {--workers= : Comma-separated list of worker codes to process (e.g., RS293,RS294)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly salary based on workers attendance';

    // private $salaryProcessingServiceInterface;

    public function __construct(private SalaryProcessingServiceInterface $salaryProcessingServiceInterface)
    {
        parent::__construct();
        // $this->salaryProcessingServiceInterface = $salaryProcessingServiceInterface;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::put('process_salary_command_running', true, now()->addMinutes(15));

        // $workerOption = $this->option('workers') ?? 'AK206';
        $workerOption = $this->option('workers');
        $workerCodes = $workerOption ? explode(',', $workerOption) : [];

        $monthOption = $this->option('month');
        if ($monthOption) {
            try {
                $date = Carbon::createFromFormat('Y-m', $monthOption);
            } catch (\Exception $e) {
                $this->error("Invalid date format. Please provide YYYY-MM format.");
                return;
            }
        } else {
            $date = Carbon::now()->subMonth();
        }
        $year = $date->year;
        $month = $date->month;

        Log::info("ProcessSalariesCommand for {$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . " started at: " . now());
        $this->info("ProcessSalariesCommand for {$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . " started at: " . now());

        try {

            $this->salaryProcessingServiceInterface->processSalaries($workerCodes, $year, $month);

            Log::info('ProcessSalariesCommand Completed at: ' . now());
            $this->info('ProcessSalariesCommand Completed at: ' . now());
        } catch (\Exception $e) {
            Log::error('ProcessSalariesCommand failed at: ' . now() . $e->getMessage());
            $this->error('ProcessSalariesCommand failed at: ' . now() . $e->getMessage());
        } finally {
            Log::info('ProcessSalariesCommand finished at: ' . now());
            $this->info('ProcessSalariesCommand finished at: ' . now());
            Cache::forget('process_salary_command_running');
        }
    }
}
