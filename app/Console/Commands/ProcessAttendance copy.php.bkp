<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Services\AttendanceProcessingService;
// use App\Services\AttendanceProcessingServiceNightShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:process
                            {--month= : Year and Month for processing attendance (YYYY-MM)}
                            {--workers= : Comma-separated list of worker codes to process (e.g., RS293,RS294)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process raw attendance data into processed_attendance table';

    // protected $attendanceProcessingService;

    /**
     * Create a new command instance.
     */
    // public function __construct(AttendanceProcessingService $attendanceProcessingService)
    public function __construct(protected AttendanceProcessingService $attendanceProcessingService)
    {
        parent::__construct();
        // $this->attendanceProcessingService = $attendanceProcessingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::put('process_attendance_running', true, now()->addMinutes(15));
        Log::info('Command ProcessAttendance started at: ' . now());
        $this->info('Command ProcessAttendance started at: ' . now());

        try {
            # Parse the month option
            $monthOption = $this->option('month');
            $month = $monthOption ? Carbon::parse($monthOption . '-01') : Carbon::now();

            $startDate = $month->copy()->startOfMonth()->toDateString();
            $endDate = $month->isCurrentMonth() ? Carbon::now()->toDateString() : $month->copy()->endOfMonth();

            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);

            if ($startDate->greaterThan($endDate)) {
                $this->error('Start date cannot be greater than end date.');
                return 1;
            }

            // $dateRange = ($startDate->equalTo($endDate)) ? [$startDate, $endDate] : $startDate->daysUntil($endDate)->toArray();

            // ✅ Use lazy CarbonPeriod, NOT toArray
            $dateRange = ($startDate->equalTo($endDate))
                ? collect([$startDate, $endDate])
                : CarbonPeriod::create($startDate, $endDate);

            // dd($dateRange);

            $workerCodes = $this->option('workers');
            $workers = $workerCodes
                ? Worker::whereIn('code', explode(',', $workerCodes))->get()->all()
                : Worker::all();



            $this->attendanceProcessingService->setDateRange($dateRange);
            $this->attendanceProcessingService->setWorkers($workers);
            $this->attendanceProcessingService->processAttendance();
        } catch (\Exception $e) {
            Log::error('Command ProcessAttendance failed at: ' . now() . $e->getMessage());
            $this->error('Command ProcessAttendance failed at: ' . now() . $e->getMessage());
        } finally {
            Log::info('Command ProcessAttendance finished at: ' . now());
            $this->info('Command ProcessAttendance finished at: ' . now());
            Cache::forget('process_attendance_running');
        }
    }
}
