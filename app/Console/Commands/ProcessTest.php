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

class ProcessTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:test
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
    {;
        $lockKey = 'process_attendance_lock';
        $lock = Cache::lock($lockKey, 3600); // 1 hour lock TTL

        if (! $lock->get()) {
            $this->info('❌ Lock already held. Skipping execution.');
            Log::info('❌ Lock already held. Skipped at: ' . now());
            return;
        }

        try {
            $this->info('🔐 Lock acquired. Running simulated long task.');
            Log::info('🔐 Lock acquired at: ' . now());

            // Simulate long task (1 minute)
            sleep(30);

            $this->info('✅ Simulated task completed.');
            Log::info('✅ Task finished at: ' . now());
        } catch (\Exception $e) {
            Log::error('🔥 Error during simulation: ' . $e->getMessage());
            $this->error('🔥 Error: ' . $e->getMessage());
        } finally {
            $lock->release();
            $this->info('🔓 Lock released.');
            Log::info('🔓 Lock released at: ' . now());
            die();
        }
    }
}
