<?php

namespace App\Console\Commands;

// use App\Services\AttendanceApiMachineOne;
// use App\Services\AttendanceApiMachineThree;
// use App\Services\AttendanceApiMachineTwo;

use App\Services\AttendanceApi\AttendanceApiMachineOne;
use App\Services\AttendanceApi\AttendanceApiMachineThree;
use App\Services\AttendanceApi\AttendanceApiMachineTwo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:fetch
                            {--month= : Year and Month for processing attendance (YYYY-MM)}
                            {--worker= : worker code to process (e.g., RS293 or RS294)}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attendance Api All machines';
    protected $empcode;
    protected $fromDate;
    protected $toDate;

    // protected $attendanceApiMachineOne;
    // protected $attendanceApiMachineTwo;
    // protected $attendanceApiMachineThree;

    public function __construct(
        protected AttendanceApiMachineOne $attendanceApiMachineOne,
        protected AttendanceApiMachineTwo $attendanceApiMachineTwo,
        protected AttendanceApiMachineThree $attendanceApiMachineThree
    ) {
        parent::__construct();
        $this->empcode = 'ALL';
        $this->fromDate = date('01/m/Y');
        $this->toDate = date('t/m/Y');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cache::put('fetch_attendance_running', true, now()->addMinutes(15));

        $lockKey = 'fetch_attendance_lock';
        $lock = Cache::lock($lockKey, 1800); // 1 hour lock TTL

        if (! $lock->get()) {
            $this->info('❌ fetch_attendance_lock already held. Skipping execution.');

            // if (now()->minute % 5 === 0) {
            //     Log::info('❌ fetch_attendance_lock already held. Skipped at: ' . now());
            // }

            if (! Cache::has('fetch_attendance_log_throttle')) {
                Log::info('❌ fetch_attendance_lock already held. Skipped at: ' . now());
                Cache::put('fetch_attendance_log_throttle', true, now()->addMinutes(5));
            }
            return;
        }


        Log::info('Command attendance:fetch started at: ' . now());
        $this->info('Command attendance:fetch started at: ' . now());

        if ($this->option('worker')) {
            $this->empcode = $this->option('worker');
        }
        if ($this->option('month')) {
            $this->fromDate = date('01/m/Y', strtotime($this->option('month') . "-01"));
            if ($this->option('month') == date('Y-m')) {
                $this->toDate = date('d/m/Y');
            } else {
                $this->toDate = date('t/m/Y', strtotime($this->option('month') . "-01"));
            }
        }

        try {
            $this->info('fetchDayShiftAttendance1 has started at: ' . now());
            Log::info('fetchDayShiftAttendance1 has started at: ' . now());
            $this->fetchDayShiftAttendance1();
            $this->info('fetchDayShiftAttendance1 has ended at: ' . now());
            Log::info('fetchDayShiftAttendance1 has ended at: ' . now());


            $this->info('fetchNightShiftAttendance1 has started at: ' . now());
            Log::info('fetchNightShiftAttendance1 has started at: ' . now());
            $this->fetchNightShiftAttendance1();
            $this->info('fetchNightShiftAttendance1 has ended at: ' . now());
            Log::info('fetchNightShiftAttendance1 has ended at: ' . now());


            $this->info('fetchHeuerWorkerAttendance has started at: ' . now());
            Log::info('fetchHeuerWorkerAttendance has started at: ' . now());
            $this->fetchHeuerWorkerAttendance();
            $this->info('fetchHeuerWorkerAttendance has ended at: ' . now());
            Log::info('fetchHeuerWorkerAttendance has ended at: ' . now());
        } catch (\Exception $e) {
            Log::error('Command attendance:fetch failed at: ' . now() . $e->getMessage());
            $this->error('Command attendance:fetch failed at: ' . now() . $e->getMessage());
        } finally {
            Log::info('Command attendance:fetch finished at: ' . now());
            $this->info('Command attendance:fetch finished at: ' . now());
            // Cache::forget('fetch_attendance_running');
            $lock->release();
            $this->info('🔓 Lock released.');
            Log::info('🔓 Lock released at: ' . now());
        }
    }

    private function fetchDayShiftAttendance1()
    {
        $this->info('Started to fetch attendance for Day Shift from machine 1');
        $result = $this->attendanceApiMachineOne->storeAttendance($this->empcode, $this->fromDate, $this->toDate);
        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
        $this->info('Completed fetching attendance for Day Shift from machine 1');

        Log::info('Completed fetching attendance for Day Shift from machine 1');
    }

    private function fetchNightShiftAttendance1()
    {
        $this->info('Started to fetch attendance for Night shift from machine 1');
        $result = $this->attendanceApiMachineTwo->storeAttendance($this->empcode, $this->fromDate, $this->toDate);
        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
        $this->info('Completed fetching attendance for Night shift from machine 1');

        Log::info('Completed fetching attendance for Night shift from machine 1');
    }

    private function fetchHeuerWorkerAttendance()
    {
        $this->info('Started to fetch attendance for Heuer Worker');
        $result = $this->attendanceApiMachineThree->storeAttendance($this->empcode, $this->fromDate, $this->toDate);
        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
        $this->info('Completed fetching attendance for Heuer Worker');
        Log::info('Completed fetching attendance for Heuer Worker');
    }
}
