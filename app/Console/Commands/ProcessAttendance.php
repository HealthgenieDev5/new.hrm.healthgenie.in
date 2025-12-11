<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Services\AttendanceProcessingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\progress;

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

        $lockKey = 'process_attendance_lock';
        $lock = Cache::lock($lockKey, 3600); // 1 hour lock TTL

        if (! $lock->get()) {
            $this->info('❌ process_attendance_lock already held. Skipping execution.');
            // if (now()->minute % 5 === 0) {
            //     Log::info('❌ process_attendance_lock already held. Skipped at: ' . now());
            // }
            if (! Cache::has('process_attendance_log_throttle')) {
                Log::info('❌ process_attendance_lock already held. Skipped at: ' . now());
                Cache::put('process_attendance_log_throttle', true, now()->addMinutes(5));
            }
            return;
        }


        // Cache::put('process_attendance_running', true, now()->addMinutes(30));

        Log::info('Command ProcessAttendance started at: ' . now());
        $this->info('Command ProcessAttendance started at: ' . now());

        try {
            $month = $this->option('month') ?? now()->format('Y-m');
            $workerCodes = $this->option('workers');

            $startDate = Carbon::parse($month . '-01')->startOfMonth();
            $endDate = Carbon::parse($month . '-01')->endOfMonth();

            $query = Worker::query()->activeBetween($startDate, $endDate);

            if ($workerCodes) {
                $query->whereIn('code', explode(',', $workerCodes));
            }


            //$query->where('id', '>=', '3');
            //$query->where('id', '<', '200');

            // $query->where('id', '>=', '200');
            // $query->where('id', '<', '400');

            // $query->where('id', '>=', '400');
            // $query->where('id', '<', '600');
            // $query->where('code', '=', 'HN960');

            // $query->where('id', '>=', '600');
            // $query->where('id', '<', '800');

            // $query->where('id', '>=', '800');
            // $query->where('id', '<', '1000');

            // $query->where('id', '>=', '1000');
            // $query->where('id', '<', '1200');

            // $query->where('id', '>=', '1200');
            // $query->where('id', '<', '1400');

            // $query->where('id', '>=', '1400');

            // $workers = $query->select('code')->get();
            $totalWorkers = $query->count();

            // $bar = $this->output->createProgressBar(count($workers));
            $bar = $this->output->createProgressBar($totalWorkers);
            $bar->start();
            $this->newLine();
            // foreach ($workers as $worker) {
            foreach ($query->select('id', 'code')->cursor() as $worker) {
                $memoryBefore = memory_get_usage(true);
                $startTime = microtime(true); // ⏱️ Start timer

                $this->output->write("\033[F");

                Artisan::call('attendance:process-worker', [
                    'code' => $worker->code,
                    '--month' => $month,
                ]);

                $endTime = microtime(true); // ⏱️ End timer
                $memoryAfter = memory_get_usage(true);

                $memoryUsed = $memoryAfter - $memoryBefore;
                $timeTaken = round($endTime - $startTime, 2); // seconds with 2 decimal points

                $bar->advance();

                $this->output->write("\n\033[2K");
                // $this->output->write("Processed: {$worker->code} - Memory consumed {$memoryUsed}");
                $this->output->write("Processed: {$worker->code} - Memory: " . $memoryUsed . " MB - Time: {$timeTaken} sec");
            }
            $bar->finish();


            $this->info("✅ All workers processed successfully.");
        } catch (\Exception $e) {
            Log::error('Command ProcessAttendance failed at: ' . now() . $e->getMessage());
            $this->error('Command ProcessAttendance failed at: ' . now() . $e->getMessage());
        } finally {
            Log::info('Command ProcessAttendance finished at: ' . now());
            $this->info('Command ProcessAttendance finished at: ' . now());
            // Cache::forget('process_attendance_running');

            $lock->release();
            $this->info('🔓 Lock released.');
            Log::info('🔓 Lock released at: ' . now());
        }
    }
}
