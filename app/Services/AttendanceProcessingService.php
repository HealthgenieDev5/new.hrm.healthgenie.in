<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\ManualPunching;
use App\Models\ProcessedAttendance;
use App\Models\ShiftOverride;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AttendanceProcessingService
{
    private $dateRange;

    private $workers;

    private $holidays;

    /**
     * Constructor to initialize date range and workers.
     *
     * @param  array|null  $dateRange  [start_date, end_date] as Carbon instances
     * @param  array|null  $workers  Array of Worker models or IDs
     */
    public function __construct(?array $dateRange = null, ?array $workers = null)
    {
        // Check if the `workers` table exists before running any query
        if (! Schema::hasTable('workers')) {
            return;
        }

        $this->dateRange = $dateRange ?? collect([Carbon::today(), Carbon::today()]); // Default to today
        // $this->workers = $workers ?? Worker::where('employment_status', 'Active')->get(); # Default to all active workers
        $this->workers = $workers; // Workers will be fetched lazily in processAttendance
        $this->holidays = [];
    }

    public function setDateRange(iterable $dateRange)
    {
        // If it's an array or collection (not CarbonPeriod), validate the range
        if (is_array($dateRange) || $dateRange instanceof \Illuminate\Support\Collection) {

            if (count($dateRange) !== 2) {
                throw new \InvalidArgumentException('Date range array or collection must have exactly two dates.');
            }

            $start = $dateRange[0];
            $end = $dateRange[1];

            if (! ($start instanceof \Carbon\Carbon) || ! ($end instanceof \Carbon\Carbon)) {
                throw new \InvalidArgumentException('Both start and end dates must be Carbon instances.');
            }

            if ($start->greaterThan($end)) {
                throw new \InvalidArgumentException('Start date cannot be greater than end date.');
            }

            // Optional: convert to simple array if collection
            $this->dateRange = [$start, $end];

            return;
        }

        // If it's CarbonPeriod or any other iterable, accept as-is
        if ($dateRange instanceof \Carbon\CarbonPeriod || is_iterable($dateRange)) {
            $this->dateRange = $dateRange;

            return;
        }

        throw new \InvalidArgumentException('Date range must be an array, Collection, or CarbonPeriod.');
    }

    /**
     * Set the workers for processing attendance.
     *
     * @param  array  $workers  Array of Worker models or IDs
     */
    public function setWorkers(array|\Illuminate\Support\Collection $workers)
    {
        if ($workers instanceof \Illuminate\Support\Collection) {
            $workers = $workers->all();
        }

        $this->workers = $workers;
    }

    /**
     * Process raw attendance data and store it in the processed_attendance table after adjustments.
     */
    public function processAttendance()
    {

        DB::connection()->disableQueryLog();

        $firstDate = collect($this->dateRange)->first();

        // Log::info("Processing attendance for : {$firstDate->format('F Y')}");

        $this->preloadHolidays();

        $dateRange = $this->dateRange;

        /* collect($this->workers)->chunk(2)->each(function ($chunk) use ($dateRange) {
            foreach ($chunk as $worker) {

                // echo "Before processing Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";

                $this->processWorkerIndividually($worker, $dateRange);

                // echo "{$worker->code}: Processed\n";
                // echo "After processing Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";
                // echo "Peak Memory so far: " . number_format(memory_get_peak_usage() / 1048576, 2) . " MB\n";
                // echo "------------------------------------\n";
            }
            DB::flushQueryLog();
            gc_collect_cycles();
        }); */

        if ($this->workers) {
            collect($this->workers)->chunk(2)->each(function ($chunk) use ($dateRange) {
                foreach ($chunk as $worker) {
                    $this->processWorkerIndividually($worker, $dateRange);
                }
                DB::flushQueryLog();
                gc_collect_cycles();
            });
        } else {
            $size = 50;
            Worker::where('employment_status', 'Active')->chunk($size, function ($workers) use ($dateRange) {
                foreach ($workers as $worker) {
                    $this->processWorkerIndividually($worker, $dateRange);
                }
                DB::flushQueryLog();
                gc_collect_cycles();
            });
        }

        // Log::info('Attendance processing completed successfully.');
    }

    public function processWorkerIndividually($worker, $dateRange)
    {
        $workerInstance = $worker;

        if (! $workerInstance || ! $workerInstance->shift) {
            return;
        }

        $joinDate = $worker->date_of_joining ? Carbon::parse($worker->date_of_joining) : null;
        $leaveDate = $worker->date_of_leaving ? Carbon::parse($worker->date_of_leaving) : null;

        $workerProcessedData = [];

        // echo "Before processWorkerAttendance Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";
        foreach ($dateRange as $date) {
            if (
                ($joinDate && $date->lt($joinDate)) ||
                ($leaveDate && $date->gt($leaveDate))
            ) {
                ProcessedAttendance::where('worker_id', $worker->id)
                    ->where('date', $date->toDateString())
                    ->limit(1)->delete();

                continue;
            }

            $processed = $this->processWorkerAttendance($workerInstance, $date);
            $workerProcessedData[] = $processed;
        }
        // echo "After processWorkerAttendance Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";

        // echo "Before sandwichAdjustment Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";
        $workerProcessedData = $this->sandwichAdjustment($workerProcessedData);
        // echo "After sandwichAdjustment Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";

        // echo "Before processBy8HrsShift Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";
        $workerProcessedData = $this->processBy8HrsShift($workerProcessedData);
        // echo "After processBy8HrsShift Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";

        // echo "Before adjustGraceMinutesForWorker Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";
        $workerProcessedData = $this->adjustGraceMinutesForWorker($workerProcessedData);
        // echo "After adjustGraceMinutesForWorker Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";

        // dd($workerProcessedData);

        // echo "Before updateOrCreate Worker ID {$worker->code}: " . number_format(memory_get_usage() / 1048576, 2) . " MB\n";

        $validColumns = Schema::getColumnListing('processed_attendance');

        $workerProcessedData = array_map(function ($record) use ($validColumns) {
            return array_intersect_key($record, array_flip($validColumns));
        }, $workerProcessedData);

        // Step 4: Insert or update processed attendance in the database
        // dd($workerProcessedData[0]);
        ProcessedAttendance::upsert(
            $workerProcessedData,
            ['worker_id', 'date']
        );

        $worker->unsetRelations();
        unset($workerInstance, $worker, $workerProcessedData, $joinDate, $leaveDate);
        gc_collect_cycles();
        clearstatcache();
    }

    private function getAttendance($worker, $date)
    {
        $shiftOverride = $this->getShiftOverride($worker->id, $date);

        if ($shiftOverride) {
            $worker->shift = $shiftOverride->shift;
        } else {
            $worker->shift = Worker::find($worker->id)->shift;
        }

        $shiftType = $worker->shift->shift_type;
        $sisterShiftType = $worker->shift->sisterShift->shift_type;

        $record = Attendance::where('empcode', $worker->code)
            ->where('date', $date->format('Y-m-d'))
            ->where('shift_type', '=', $shiftType)
            ->first();
        if (! $record || ! $record?->intime) {
            $record = Attendance::where('empcode', $worker->code)
                ->where('date', $date->format('Y-m-d'))
                ->where('shift_type', '=', $sisterShiftType)
                ->first();
        }

        try {
            $record->intime = $record?->intime ? Carbon::parse($record->intime) : null;
            $record->outtime = $record?->outtime ? Carbon::parse($record->outtime) : null;
        } catch (\Throwable $th) {
            // dd($date);
        }

        return $record;
    }

    public function adjustNightShiftHeuerAttendance($worker, $date)
    {
        $prevDate = $date->copy()->subDay()->startOfDay();
        $currentDate = $date->copy()->startOfDay();
        $nextDate = $date->copy()->addDay()->startOfDay();

        $prevRecord = $this->getAttendance($worker, $prevDate, true);
        $record = $this->getAttendance($worker, $currentDate, false);
        $nextRecord = $this->getAttendance($worker, $nextDate, true);

        if (! $record) {
            return null;
        }

        $morningThreshold = Carbon::createFromTime(9, 45, 0);
        $eveningThreshold = Carbon::createFromTime(19, 15, 0);

        $shiftOverride = $this->getShiftOverride($worker->id, $date);
        if ($shiftOverride) {
            $shift = $shiftOverride->shift;
        } else {
            $shift = Worker::find($worker->id)->shift;
        }

        // if ($date->format('Y-m-d') == '2025-03-24') {
        //     dd([
        //         $record->intime,
        //         $record->outtime
        //     ]);
        // }

        if ($shift->shift_type == 'night' && $record->shift_type == 'day') {
            if (
                $record->intime &&
                $record->intime->greaterThan($eveningThreshold)   // Today first punch (~after 07:15PM)
                && ! $record->outtime
            ) {
                $record->intime = $record->intime;
            } else {
                $record->intime = $record?->outtime ?? null;
            }

            $nextShiftOverride = $this->getShiftOverride($worker->id, $nextDate);
            if ($nextShiftOverride) {
                $nextShift = $nextShiftOverride->shift;
            } else {
                $nextShift = Worker::find($worker->id)->shift;
            }

            if (
                $nextRecord &&
                $nextRecord->intime &&
                $nextRecord->intime->lessThan($morningThreshold)   // next day first punch (~before 08:45AM)
                // && $nextShift->shift_type == 'night'
            ) {
                // $record->outtime = $nextRecord->intime;

                $nextRecordInTime = $nextRecord->intime;
                $recordOutTime = $nextRecordInTime->copy()->addDay();
                $record->outtime = $recordOutTime;
            } else {
                $record->outtime = null;
            }
        }

        // if ($date->format('Y-m-d') == '2025-04-13') {
        //     dd([
        //         $record->intime,
        //         $record->outtime
        //     ]);
        // }

        return $record;
    }

    public function adjustDayShiftHeuerAttendance($worker, $date)
    {
        $prevDate = $date->copy()->subDay()->startOfDay();
        $currentDate = $date->copy()->startOfDay();
        $nextDate = $date->copy()->addDay()->startOfDay();

        // $prevRecord = $this->getAttendance($worker, $prevDate, true);
        $record = $this->getAttendance($worker, $currentDate, false);
        // $nextRecord = $this->getAttendance($worker, $nextDate, true);

        if (! $record) {
            return null;
        }

        $morningThreshold = Carbon::createFromTime(9, 45, 0);
        $eveningThreshold = Carbon::createFromTime(19, 15, 0);

        $prevShiftOverride = $this->getShiftOverride($worker->id, $prevDate);
        if ($prevShiftOverride) {
            $prevShift = $prevShiftOverride->shift;
        } else {
            $prevShift = Worker::find($worker->id)->shift;
        }

        $shiftOverride = $this->getShiftOverride($worker->id, $date);
        if ($shiftOverride) {
            $shift = $shiftOverride->shift;
        } else {
            $shift = Worker::find($worker->id)->shift;
        }

        if (($prevShift->shift_type == 'night' && $shift->shift_type == 'day') && $record->shift_type == 'day') {
            // Get first punch after prev punch out (current day's first punch)
            $recordInTime = $record->intime ? $currentDate->copy()->setTime($record->intime->hour, $record->intime->minute, $record->intime->second) : null;
            $fromDateTime = $recordInTime ? $recordInTime->copy()->addMinutes(1) : null;
            $toDateTime = $currentDate->copy()->setTimeFromTimeString('23:59');
            $punchData = ($fromDateTime && $toDateTime) ?
                $this->getAllPunchData($worker->code, $fromDateTime->format('d/m/Y_H:i'), $toDateTime->format('d/m/Y_H:i')) :
                null;
            // dd($punchData);
            // because $punchData is descending order so I will use the last record of the array as first punch post night shift punch out
            // $lastPunchOfCurrentDateAfterNightPunchOutArray = end($punchData) ?? null;
            // $lastPunchOfCurrentDateAfterNightPunchOutDate = $lastPunchOfCurrentDateAfterNightPunchOutArray['PunchDate'] ?? null;
            // $lastPunchOfCurrentDateAfterNightPunchOutDateParsed = $lastPunchOfCurrentDateAfterNightPunchOutDate ? Carbon::createFromFormat('d/m/Y H:i:s', $lastPunchOfCurrentDateAfterNightPunchOutDate) : null;

            if (! empty($punchData)) {
                // because $punchData is descending order so I will use the last record of the array as first punch post night shift punch out
                $lastPunchOfCurrentDateAfterNightPunchOutArray = $punchData ? end($punchData) : null;
                $lastPunchOfCurrentDateAfterNightPunchOutDate = $lastPunchOfCurrentDateAfterNightPunchOutArray ? $lastPunchOfCurrentDateAfterNightPunchOutArray['PunchDate'] : null;
                $lastPunchOfCurrentDateAfterNightPunchOutDateParsed = $lastPunchOfCurrentDateAfterNightPunchOutDate ? Carbon::createFromFormat('d/m/Y H:i:s', $lastPunchOfCurrentDateAfterNightPunchOutDate) : null;
            } else {
                $lastPunchOfCurrentDateAfterNightPunchOutDateParsed = null;
            }

            $record->intime = $lastPunchOfCurrentDateAfterNightPunchOutDateParsed ? $lastPunchOfCurrentDateAfterNightPunchOutDateParsed->format('H:i') : null;
            $recordOutTime = $record->outtime ? $record->outtime->format('H:i') : null;
            $record->outtime = $recordOutTime;
            // dd($record->intime);

            if ($record->intime == $record->outtime) {
                $record->outtime = null;
            }
        }

        return $record;
    }

    private function getAllPunchData($empcode, $fromDateTime, $toDateTime)
    {
        $url = 'https://api.etimeoffice.com/api/DownloadPunchData';
        $corporateId = config('services.machine_3_api.corporate_id');
        $username = config('services.machine_3_api.username');
        $password = config('services.machine_3_api.password');
        $auth = base64_encode("{$corporateId}:{$username}:{$password}:true");
        $postData = ['Empcode' => $empcode, 'FromDate' => $fromDateTime, 'ToDate' => $toDateTime];
        $response = Http::withHeaders(['Authorization' => "Basic {$auth}"])->get($url, $postData);
        if ($response->successful()) {
            $data = $response->json();
            $punchData = $data['PunchData'] ?? [];

            return $punchData;
        }

        return null;
    }

    /**
     * Process a single worker's attendance for a given date.
     */
    private function processWorkerAttendance(Worker $worker, Carbon $date): array
    {

        // Check for shift override
        $shiftOverride = $this->getShiftOverride($worker->id, $date);
        if ($shiftOverride) {
            $worker->shift = $shiftOverride->shift; // Override the worker's shift
        } else {
            $worker->shift = Worker::find($worker->id)->shift;
        }

        $sisterShift = $worker->shift->sisterShift;
        $sisterShiftType = $sisterShift->shift_type;

        $shiftDetails = $this->getShiftDetails($worker, false);

        $originalShiftStart = $shiftDetails['shiftStart'];
        $originalShiftEnd = $shiftDetails['shiftEnd'];
        $originalShiftType = $shiftDetails['shiftType'];
        $originalShiftHours = $shiftDetails['shiftDuration'];
        $originalGraceMinutes = (float) $shiftDetails['graceMinutes'];
        $originalWeekOffDay = $shiftDetails['weekOffDay'];
        $originalShiftSubType = $shiftDetails['shiftSubType'];


        if ($date->format('Y-m-d') <= '2026-05-07') {
            $expandedShiftDetails = $worker->shift?->is_internal ? $shiftDetails : $this->getShiftDetails($worker, true);
        } else {
            $expandedShiftDetails = $worker->shift?->is_internal ? $shiftDetails : $this->getShiftDetails($worker, false);
        }



        $shiftStart = $expandedShiftDetails['shiftStart'];
        $shiftEnd = $expandedShiftDetails['shiftEnd'];
        $shiftType = $expandedShiftDetails['shiftType'];
        $shiftHours = (float) $expandedShiftDetails['shiftDuration'];
        $defaultGraceMinutes = (float) $expandedShiftDetails['graceMinutes'];
        $weekOffDay = $expandedShiftDetails['weekOffDay'];
        $shiftSubType = $expandedShiftDetails['shiftSubType'];

        if (strtotime($date->format('Y-m-d')) >= strtotime('2025-02-11')) {
            $record = Attendance::where('empcode', $worker->code)
                ->where('date', $date->format('Y-m-d'))
                ->where('shift_type', '=', $originalShiftType)
                ->first();

            $sisterRecord = Attendance::where('empcode', $worker->code)
                ->where('date', $date->format('Y-m-d'))
                ->where('shift_type', '=', $sisterShiftType)
                ->first();
        } else {
            $record = Attendance::where('empcode', $worker->code)
                ->where('date', $date->format('Y-m-d'))
                ->first();
            $sisterRecord = null;
        }

        $inTime = $inTimeBkp = $record?->intime ? Carbon::parse($record->intime) : null;
        $outTime = $outTimeBkp = $record?->outtime ? Carbon::parse($record->outtime) : null;

        $rec1 = [];
        if ($worker->psp->code == 'HN' && $worker->shift->shift_type == 'night') {
            $heuerAttendance = $this->adjustNightShiftHeuerAttendance($worker, $date, $shiftType);
            $inTime = $inTimeBkp = $heuerAttendance?->intime ? Carbon::parse($heuerAttendance->intime) : null;
            $outTime = $outTimeBkp = $heuerAttendance?->outtime ? Carbon::parse($heuerAttendance->outtime) : null;
        } elseif ($worker->psp->code == 'HN' && $worker->shift->shift_type == 'day') {
            $heuerAttendance = $this->adjustDayShiftHeuerAttendance($worker, $date, $shiftType);
            $inTime = $inTimeBkp = $heuerAttendance?->intime ? Carbon::parse($heuerAttendance->intime) : null;
            $outTime = $outTimeBkp = $heuerAttendance?->outtime ? Carbon::parse($heuerAttendance->outtime) : null;
        }

        // Begin:: Override intime and outtime
        $manualPunching = $this->getManualPunching($worker->id, $date);
        if ($manualPunching) {
            $inTime = $inTimeBkp = $manualPunching?->intime ? Carbon::parse($manualPunching->intime) : null;
            $outTime = $outTimeBkp = $manualPunching?->outtime ? Carbon::parse($manualPunching->outtime) : null;
        }
        // End:: Override intime and outtime

        $inTimeSisterShift = $sisterRecord?->intime ? Carbon::parse($sisterRecord->intime) : null;
        $outTimeSisterShift = $sisterRecord?->outtime ? Carbon::parse($sisterRecord->outtime) : null;

        $isWrongShift = false;
        $dx = 'test';
        // if (!$inTime && !$outTime && $inTimeSisterShift && $outTimeSisterShift) {
        if (! $inTime && ! $outTime && $inTimeSisterShift) {
            $inTime = null;
            $outTime = null;
            if ($worker->psp->code == 'HN' && $shiftType == 'night') {
                $isWrongShift = false;
                $dx = '1.1';
            } else {
                $isWrongShift = true;
                $dx = '1.2';
            }
        } elseif ($shiftType == 'night' && $outTime && $inTime) {
            if ($outTime->greaterThan($inTime)) {
                $outTime = $outTime;
                $st = 'not isWrongShift';
                $dx = '2.1';
            } else {
                // Added 1 day for calculation using Carbon, but in the data it will show only time
                // Reason for adding is In the night shift the workers will punch out next day, so calculations become easier if I add 1 day
                $outTime = $outTime?->addDay();
                $dx = '2.2';
            }
        } elseif ($shiftType == 'day' && $outTime && $inTime) {
            $dx = '3.1';
            if ($inTime->greaterThan($outTime)) {
                $inTime = null;
                $outTime = null;
                $isWrongShift = true;
                $dx = '3.2';
            }
        }

        // /** @var \App\Models\User|null $user */
        // $user = Auth::user();
        // if ($user->hasRole('Super Admin')) {
        //     dd([
        //         'date' => $date->format('Y-m-d'),
        //         'shiftType' => $shiftType,
        //         'isWrongShift' => $isWrongShift,
        //         'dx' => $dx,
        //         'psp' => $worker->psp->name
        //         // $st
        //     ]);
        // }

        $lateComingMinutesOriginal = 0.0;
        $earlyGoingMinutesOriginal = 0.0;
        $lateComingMinutes = 0.0;
        $earlyGoingMinutes = 0.0;
        $nonWorkingMinutes = 0.0;
        $adjustableMinutes = 0.0;
        $inTimeProcessed = null;
        $outTimeProcessed = null;
        $totalWorkHours = 0.0;
        $workHoursWithinShift = 0.0;
        $graceMinutes = 0.0;
        $adjustedMinutes = 0.0;
        $status = 'A';
        $statusRemarks = '';

        $isMissedPunch = $this->isMissedPunch($inTime, $outTime);
        $isAbsent = $this->isAbsent($inTime, $outTime);
        $isHoliday = $this->isHoliday($date);
        $isSpecialHoliday = $this->isSpecialHoliday($worker, $date);
        $isWeekoff = $this->isWeekoff($weekOffDay, $date);

        if ($inTime || $outTime) {

            $inTimeProcessed = $this->getInTimeProcessed($inTime, $shiftStart);
            $outTimeProcessed = $this->getOutTimeProcessed($outTime, $shiftEnd, $shiftType);

            if ($inTime) {
                $lateComingMinutesOriginal = $this->getTimeDifference($inTime, $shiftStart);
                $lateComingMinutes = $inTimeProcessed && $inTimeProcessed->greaterThan($shiftStart) ? abs($this->getTimeDifference($inTimeProcessed, $shiftStart)) : 0.0;
            }

            if ($outTime) {
                $earlyGoingMinutesOriginal = $this->getTimeDifference($outTime, $shiftEnd);
                $earlyGoingMinutes = $outTimeProcessed && $outTimeProcessed->lessThan($shiftEnd) ? abs($this->getTimeDifference($outTimeProcessed, $shiftEnd)) : 0.0;
            }

            // if($date->format('Y-m-d') == '2025-04-13'){
            //     dd(
            //         [
            //             $shiftEnd,
            //             $outTimeProcessed,
            //         ]
            //     );
            // }

            $nonWorkingMinutes = $lateComingMinutes + $earlyGoingMinutes;
            $adjustableMinutes = $nonWorkingMinutes;

            if ($inTime && $outTime) {
                $totalWorkHours = $this->getTimeDifference($inTime, $outTime);
                $workHoursWithinShift = $this->getTimeDifference($inTimeProcessed, $outTimeProcessed);
                $graceMinutes = $this->getGraceMinutes($defaultGraceMinutes, $workHoursWithinShift, $shiftHours);
            }

            if ($isMissedPunch) {
                // Case 2: Missed Punch (One of in_time or out_time is missing)
                $status = 'M/P'; // Missed Punch
                $statusRemarks = 'Missed punch';
                $adjustableMinutes = $shiftHours;
            } elseif ($workHoursWithinShift >= $shiftHours) {
                // Case 3: Present (Worked full shift hours)
                $status = 'P'; // Present
                $statusRemarks = 'Present';
            } elseif ($workHoursWithinShift >= ($shiftHours / 2)) {
                // Case 4: Half Day (Worked at least half shift hours)
                $status = 'H/D'; // Half Day
                $statusRemarks = 'Worked half day';
            } elseif ($workHoursWithinShift < ($shiftHours / 2)) {
                // Case 5: Absent (Work hours less than half shift hours)
                $status = 'A'; // Absent
                $statusRemarks = 'Did not work for at least half of shift hours';
                // $adjustableMinutes = $shiftHours;
            } else {
                // Fallback: Undefined (This should rarely, if ever, occur)
                $status = 'UND'; // Undefined
                $statusRemarks = 'Undefined status - Please check the data';
                $adjustableMinutes = $shiftHours;
            }
        } elseif ($isWrongShift) {
            $status = 'WRONG SHIFT'; // Absent
            $statusRemarks = 'Wrong Shift';
            $adjustableMinutes = $shiftHours;
            $inTimeProcessed = $inTimeBkp;
            $outTimeProcessed = $outTimeBkp;
        } elseif ($isSpecialHoliday) {
            $holiday = Holiday::where('date', $date)->where('code', 'SPL HL')->first();
            if ($holiday) {
                $status = $holiday->code;
                $statusRemarks = $holiday->name;
            } else {
                $status = 'SPL HL';
                $statusRemarks = 'Special Holiday';
            }
        } elseif ($isHoliday) {
            $holiday = Holiday::where('date', $date)->first();
            // dd($holiday->code);
            if ($holiday) {
                $status = $holiday->code;
                $statusRemarks = $holiday->name;
            } else {
                $status = 'HL';
                $statusRemarks = 'Holiday';
            }
        } elseif ($isWeekoff) {
            $status = 'W/O';
            $statusRemarks = 'Week off';
        } elseif ($isAbsent) {
            $status = 'A'; // Absent
            $statusRemarks = 'Absent';
            $adjustableMinutes = $shiftHours;
        }

        $return = [
            'worker_id' => $worker->id,
            'date' => $date?->format('Y-m-d'),

            'original_shift_start' => $originalShiftStart?->format('H:i:s'),
            'original_shift_end' => $originalShiftEnd?->format('H:i:s'),
            'original_shift_type' => $originalShiftType,
            'original_shift_hours' => $originalShiftHours,

            'shift_start' => $shiftStart?->format('H:i:s'),
            'shift_end' => $shiftEnd?->format('H:i:s'),
            'shift_type' => $shiftType,
            'shift_hours' => $shiftHours,
            'shift_sub_type' => $shiftSubType,
            'shift_is_internal' => $worker->shift?->is_internal,

            'in_time' => $inTime?->format('H:i:s'),
            'out_time' => $outTime?->format('H:i:s'),
            'in_time_processed' => $inTimeProcessed?->format('H:i:s'),
            'out_time_processed' => $outTimeProcessed?->format('H:i:s'),
            'late_coming_minutes_original' => $lateComingMinutesOriginal,
            'early_going_minutes_original' => $earlyGoingMinutesOriginal,
            'late_coming_minutes' => $lateComingMinutes,
            'early_going_minutes' => $earlyGoingMinutes,

            // 'non_working_minutes' => $nonWorkingMinutes,
            'adjustable_minutes' => $adjustableMinutes,
            'adjusted_minutes' => $adjustedMinutes,

            'is_missed_punch' => $isMissedPunch,
            'is_absent' => $isAbsent,
            'is_holiday' => $isHoliday,
            'is_weekoff' => $isWeekoff,

            'total_work_hours' => $totalWorkHours,
            'work_hours_within_shift' => $workHoursWithinShift,
            'grace_minutes' => $graceMinutes,

            'status' => $status,
            'status_remarks' => $statusRemarks,
        ];

        // if ($return['worker_id'] == 258 && !empty($return['in_time']) && !empty($return['out_time'])) {
        //     dd($return);
        // }
        return $return;
    }

    private function adjustGraceMinutesForWorker(array $processedData): array
    {
        // Step 1: Sort by total_late (late_coming + early_going)
        usort($processedData, function ($a, $b) {
            return $a['adjustable_minutes'] <=> ($b['adjustable_minutes']);
        });

        // Step 2: Calculate total grace minutes
        $totalGraceMinutes = array_sum(array_column($processedData, 'grace_minutes'));

        // Step 3: Adjust late minutes using grace minutes
        foreach ($processedData as &$day) {
            $adjustableMinutes = $day['adjustable_minutes'];

            if ($day['status'] == 'HL') { // Skip days that are holidays
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'NH') { // Skip days that are holidays
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'RH') { // Skip days that are Religious holidays
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'SPL HL') { // Skip days that are Special holidays
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'W/O') { // Skip days that are week offs
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'A') { // Skip absent days
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'M/P') { // Skip absent days
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($day['status'] == 'S/W') { // Skip sandwich days
                $day['adjusted_minutes'] = 0;

                continue;
            } elseif ($adjustableMinutes <= $totalGraceMinutes) { // Adjust non-working minutes with available grace minutes
                // Fully adjust the non-working minutes
                $day['adjusted_minutes'] = $adjustableMinutes;
                $totalGraceMinutes -= $adjustableMinutes;
                $day['status'] = 'P'; // Mark as Present
                $day['status_remarks'] = $adjustableMinutes ? "{$adjustableMinutes} minutes adjusted, marked as full day." : $day['status_remarks'];
            }
        }

        return $processedData;
    }

    /* private function sandwichAdjustment(&$attendanceRecords)
    {
        $totalRecords = count($attendanceRecords);

        for ($i = 0; $i < $totalRecords; $i++) {
            // Reference current record
            $currentDay = &$attendanceRecords[$i];

            // Check if the current day is a Week Off (W/O), Holiday (HL), or Religious Holiday (RH)
            if (in_array($currentDay['status'], ["W/O", "HL", "RH"])) {

                // Check for previous absent day
                $prevAbsent = false;
                $prevIndex = $i - 1;
                while ($prevIndex >= 0 && in_array($attendanceRecords[$prevIndex]['status'], ["W/O", "HL", "RH"])) {
                    $prevIndex--; // Move back if consecutive holidays
                }
                if ($prevIndex >= 0 && in_array($attendanceRecords[$prevIndex]['status'], ["A", "M/P"])) {
                    $prevAbsent = true;
                }

                // Check for next absent day
                $nextAbsent = false;
                $nextIndex = $i + 1;
                while ($nextIndex < $totalRecords && in_array($attendanceRecords[$nextIndex]['status'], ["W/O", "HL", "RH"])) {
                    $nextIndex++; // Move forward if consecutive holidays
                }
                if ($nextIndex < $totalRecords && in_array($attendanceRecords[$nextIndex]['status'], ["A", "M/P"])) {
                    $nextAbsent = true;
                }

                // If both sides have absences, mark this and consecutive off days as S/W
                if ($prevAbsent && $nextAbsent) {
                    for ($j = $prevIndex + 1; $j < $nextIndex; $j++) {
                        if (in_array($attendanceRecords[$j]['status'], ["W/O", "HL", "RH"])) {
                            $attendanceRecords[$j]['status'] = "S/W";
                            $attendanceRecords[$j]['status_remarks'] = "Sandwich Leave";
                        }
                    }
                }
            }
        }

        return $attendanceRecords;
    } */

    private function sandwichAdjustment(&$attendanceRecords)
    {
        $totalRecords = count($attendanceRecords);
        if ($totalRecords === 0) {
            return $attendanceRecords;
        }

        // Define status arrays once for reuse
        $holidayStatuses = ['W/O', 'HL', 'RH', 'SPL HL', 'NH', 'S/W'];
        $absentStatuses = ['A', 'M/P'];

        // Assuming all records in $attendanceRecords are for the same worker
        // and that worker_id is present in each record.
        $workerId = $attendanceRecords[0]['worker_id'];

        for ($i = 0; $i < $totalRecords; $i++) {
            // Reference current record
            $currentDay = &$attendanceRecords[$i];

            // Check if the current day is a Week Off (W/O), Holiday (HL), or Religious Holiday (RH)
            // Also include "S/W" in this check in case of reprocessing, to correctly identify boundaries.
            if (in_array($currentDay['status'], $holidayStatuses)) {

                // Check for previous absent day
                $prevAbsent = false;
                $effectivePrevDayIndex = $i - 1; // This index is for iterating within $attendanceRecords

                // Iterate backwards within the current array
                while ($effectivePrevDayIndex >= 0 && in_array($attendanceRecords[$effectivePrevDayIndex]['status'], $holidayStatuses)) {
                    $effectivePrevDayIndex--;
                }

                if ($effectivePrevDayIndex >= 0) { // Found a non-off/holiday day within the current array
                    if (in_array($attendanceRecords[$effectivePrevDayIndex]['status'], $absentStatuses)) {
                        $prevAbsent = true;
                    }
                } else {
                    // Reached the beginning of the array, need to check DB for the day before the first record
                    // Keep checking previous days until we find a non-holiday working day
                    $firstRecordDate = Carbon::parse($attendanceRecords[0]['date']);
                    $dayBefore = $firstRecordDate->copy()->subDay();

                    while (true) {
                        $prevDbRecord = ProcessedAttendance::where('worker_id', $workerId)
                            ->where('date', $dayBefore->toDateString())
                            ->first();

                        if (! $prevDbRecord) {
                            break; // No record found, stop checking
                        }

                        if (in_array($prevDbRecord->status, $holidayStatuses)) {
                            // Previous day is also a holiday/week off, keep going back
                            $dayBefore = $dayBefore->copy()->subDay();

                            continue;
                        }

                        // Found a working day, check if it's absent
                        if (in_array($prevDbRecord->status, $absentStatuses)) {
                            $prevAbsent = true;
                        }
                        break;
                    }
                }

                // Check for next absent day
                $nextAbsent = false;
                $effectiveNextDayIndex = $i + 1; // This index is for iterating within $attendanceRecords

                // Iterate forwards within the current array
                while ($effectiveNextDayIndex < $totalRecords && in_array($attendanceRecords[$effectiveNextDayIndex]['status'], $holidayStatuses)) {
                    $effectiveNextDayIndex++;
                }

                if ($effectiveNextDayIndex < $totalRecords) { // Found a non-off/holiday day within the current array
                    if (in_array($attendanceRecords[$effectiveNextDayIndex]['status'], $absentStatuses)) {
                        $nextAbsent = true;
                    }
                } else {
                    // Reached the end of the array, need to check DB for the day after the last record
                    // Keep checking next days until we find a non-holiday working day
                    $lastRecordDate = Carbon::parse($attendanceRecords[$totalRecords - 1]['date']);
                    $dayAfter = $lastRecordDate->copy()->addDay();

                    while (true) {
                        $nextDbRecord = ProcessedAttendance::where('worker_id', $workerId)
                            ->where('date', $dayAfter->toDateString())
                            ->first();

                        if (! $nextDbRecord) {
                            break; // No record found, stop checking
                        }

                        if (in_array($nextDbRecord->status, $holidayStatuses)) {
                            // Next day is also a holiday/week off, keep going forward
                            $dayAfter = $dayAfter->copy()->addDay();

                            continue;
                        }

                        // Found a working day, check if it's absent
                        if (in_array($nextDbRecord->status, $absentStatuses)) {
                            $nextAbsent = true;
                        }
                        break;
                    }
                }

                // If both sides have absences, mark this and consecutive off days as S/W
                if ($prevAbsent && $nextAbsent) {
                    // Mark all days from (effectivePrevDayIndex + 1) to (effectiveNextDayIndex - 1)
                    // that are currently W/O, HL, or RH as S/W, *within the current $attendanceRecords array*.
                    for ($j = $effectivePrevDayIndex + 1; $j < $effectiveNextDayIndex; $j++) {
                        // Ensure $j is a valid index for the current $attendanceRecords array
                        if ($j >= 0 && $j < $totalRecords) {
                            if (in_array($attendanceRecords[$j]['status'], $holidayStatuses)) {
                                $attendanceRecords[$j]['status'] = 'S/W';
                                $attendanceRecords[$j]['status_remarks'] = 'Sandwich Leave';
                            }
                        }
                    }
                }
            }
        }

        return $attendanceRecords;
    }

    /**
     * Preload Holidays.
     */
    private function preloadHolidays()
    {
        // $dates = array_map(fn($date) => $date->format('Y-m-d'), $this->dateRange);

        $dates = [];
        foreach ($this->dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // $this->holidays = Holiday::whereIn('date', $dates)->pluck('date')->toArray();
        $this->holidays = Holiday::whereIn('date', $dates)
            ->where('code', '!=', 'SPL HL')
            ->pluck('date')->toArray();

        unset($dates);
        gc_collect_cycles();
    }

    /**
     * Check if the given date is a holiday.
     */
    private function isHoliday(Carbon $date): bool
    {
        return in_array($date->format('Y-m-d'), $this->holidays);
    }

    /**
     * Check if the given date is a special holiday assigned to the worker.
     */
    private function isSpecialHoliday(Worker $worker, Carbon $date): bool
    {
        return Holiday::where('date', $date->format('Y-m-d'))
            ->where('code', 'SPL HL')
            ->whereHas('workers', function ($query) use ($worker) {
                $query->where('worker_id', $worker->id);
            })
            ->exists();
    }

    /**
     * Get processed in-time.
     */
    private function getInTimeProcessed(?Carbon $inTime, Carbon $shiftStart): ?Carbon
    {
        if (! $inTime) {
            return null;
        }

        // Adjust in-time if it is earlier than the shift start
        return $inTime->lessThan($shiftStart) ? $shiftStart : $inTime;
    }

    /**
     * Get processed out-time.
     */
    private function getOutTimeProcessed(?Carbon $outTime, Carbon $shiftEnd, string $shiftType): ?Carbon
    {
        if (! $outTime) {
            return null;
        }

        // Adjust shift end to the next day only for night shifts
        // if ($shiftType === 'night') {
        //     $shiftEnd->addDay();
        // }

        // Ensure out-time doesn't exceed the shift end
        return $outTime->greaterThan($shiftEnd) ? $shiftEnd : $outTime;
    }

    /**
     * Check if there is a missed punch.
     */
    private function isMissedPunch(?Carbon $inTime, ?Carbon $outTime): bool
    {
        return ! $inTime || ! $outTime;
    }

    /**
     * Check if the worker is absent.
     */
    private function isAbsent(?Carbon $inTime, ?Carbon $outTime): bool
    {
        return ! $inTime && ! $outTime;
    }

    /**
     * Check if the given date is a week off for the worker.
     *
     * @param  Worker  $worker
     */
    private function isWeekoff(string $weekOffDay, Carbon $date): bool
    {
        $dayName = strtolower($date->format('l')); // e.g., 'sunday'

        return strtolower($weekOffDay) === $dayName;
    }

    /**
     * Calculate grace minutes for a worker based on work hours within the shift.
     *
     * @param  Worker  $worker
     * @param  int  $workHoursWithinShift
     * @return int Grace minutes
     */
    private function getGraceMinutes(float $defaultGraceMinutes, float $workHoursWithinShift, float $shiftHours): float
    {
        $halfDayThreshold = $shiftHours / 2;

        return $workHoursWithinShift >= $halfDayThreshold ? $defaultGraceMinutes : 0.0;
    }

    /**
     * Expand shift by 33% add half of that on both sides.
     *
     * @param  bool  $expand  default true
     * @return array Shift details
     */
    private function getShiftDetailsBkp(Worker $worker, bool $expand = true): array
    {
        // Validate worker and shift details
        if (! $worker || ! $worker->shift || ! $worker->shift->in_time || ! $worker->shift->out_time || ! $worker->shift->shift_type) {
            Log::warning("Invalid worker or shift details provided. Returning default values. worker: {$worker}, \n worker->shift");

            return [
                'shiftStart' => null,
                'shiftEnd' => null,
                'shiftType' => null,
                'shiftDuration' => 0,
                'graceMinutes' => 0,
                'weekOffDay' => null,
                'variation_33' => 0,
            ];
        }

        $shiftStart = Carbon::parse($worker->shift->in_time);
        $shiftEnd = Carbon::parse($worker->shift->out_time);
        $shiftType = $worker->shift->shift_type; // 'day' or 'night'

        $graceTime = Carbon::createFromTimeString($worker->shift->grace_minutes);

        // if ($worker->shift) {
        //     dd($graceTime);
        // }

        $graceMinutes = $graceTime->hour * 60 + $graceTime->minute + $graceTime->second / 60;

        $weekOffDay = $worker->shift->week_off; // 'day' or 'night'

        // If the shift is a night shift, adjust the end time to the next day
        if ($shiftType === 'night' && $shiftEnd->lessThan($shiftStart)) {
            $shiftEnd->addDay();
        }

        // Calculate the total shift duration (in minutes)
        $shiftDuration = $shiftStart->diffInMinutes($shiftEnd);

        // If no expansion is needed, return original shift details
        if (! $expand) {
            return [
                'shiftStart' => $shiftStart,
                'shiftEnd' => $shiftEnd,
                'shiftType' => $shiftType,
                'graceMinutes' => $graceMinutes,
                'shiftDuration' => $shiftDuration,
                'weekOffDay' => $weekOffDay,
                'variation_33' => 0, // No variation applied
            ];
        }

        // Calculate 33% variation
        $variation_33 = $shiftDuration / 2; // 33% of the original shift duration
        $variation_33_by_2 = $variation_33 / 2; // Half of the 33% variation

        // Adjust the shift start and end times
        $newShiftStart = $shiftStart->copy()->subMinutes($variation_33_by_2);
        $newShiftEnd = $shiftEnd->copy()->addMinutes($variation_33_by_2);
        $newGraceMinutes = $graceMinutes * 1.5;

        return [
            'shiftStart' => $newShiftStart, // Carbon instance
            'shiftEnd' => $newShiftEnd,     // Carbon instance
            'shiftType' => $shiftType,
            'graceMinutes' => $newGraceMinutes,
            'shiftDuration' => $shiftDuration + $variation_33, // Expanded shift duration
            'variation_33' => $variation_33,
            'weekOffDay' => $weekOffDay,
        ];
    }

    private function getShiftDetails(Worker $worker, bool $expand = true): array
    {
        // Validate worker and shift details
        if (! $worker || ! $worker->shift || ! $worker->shift->in_time || ! $worker->shift->out_time || ! $worker->shift->shift_type) {
            Log::warning("Invalid worker or shift details provided. Returning default values. worker: {$worker}, \n worker->shift");

            return [
                'shiftStart' => null,
                'shiftEnd' => null,
                'shiftType' => null,
                'shiftDuration' => 0,
                'graceMinutes' => 0,
                'weekOffDay' => null,
                'variation_33' => 0,
                'shiftSubType' => null,
            ];
        }

        $shiftStart = Carbon::parse($worker->shift->in_time);
        $shiftEnd = Carbon::parse($worker->shift->out_time);
        $shiftType = $worker->shift->shift_type; // 'day' or 'night'
        $shiftSubType = $worker->shift->sub_type;

        $graceTime = Carbon::createFromTimeString($worker->shift->grace_minutes);

        // if ($worker->shift) {
        //     dd($graceTime);
        // }

        $graceMinutes = $graceTime->hour * 60 + $graceTime->minute + $graceTime->second / 60;

        $weekOffDay = $worker->shift->week_off; // 'day' or 'night'

        // If the shift is a night shift, adjust the end time to the next day
        if ($shiftType === 'night' && $shiftEnd->lessThan($shiftStart)) {
            $shiftEnd->addDay();
        }

        // Calculate the total shift duration (in minutes)
        $shiftDuration = $shiftStart->diffInMinutes($shiftEnd);

        // If no expansion is needed, return original shift details
        if (! $expand) {
            return [
                'shiftStart' => $shiftStart,
                'shiftEnd' => $shiftEnd,
                'shiftType' => $shiftType,
                'graceMinutes' => $graceMinutes,
                'shiftDuration' => $shiftDuration,
                'weekOffDay' => $weekOffDay,
                'variation_33' => 0, // No variation applied
                'shiftSubType' => $shiftSubType, // No variation applied
            ];
        }

        // Calculate 33% variation
        $variation_33 = $shiftDuration / 2; // 33% of the original shift duration
        $variation_33_by_2 = $variation_33 / 2; // Half of the 33% variation

        if ($shiftSubType == 'regular') {
            $variation_33 = $shiftDuration / 2;
            $variation_33_by_2 = $variation_33 / 2;
        } elseif ($shiftSubType == 'reduced') {
            $variation_33 = $shiftDuration * 43.75 / 100;
            $variation_33_by_2 = $variation_33 / 2;
        } else {
            $variation_33 = 0;
            $variation_33_by_2 = 0;
        }

        // Adjust the shift start and end times
        // $newShiftStart = $shiftStart->copy()->subMinutes($variation_33_by_2);
        $newShiftStart = $shiftStart;
        $newShiftEnd = $shiftEnd->copy()->addMinutes($variation_33);
        $newGraceMinutes = $graceMinutes * 1.5;

        return [
            'shiftStart' => $newShiftStart, // Carbon instance
            'shiftEnd' => $newShiftEnd,     // Carbon instance
            'shiftType' => $shiftType,
            'graceMinutes' => $newGraceMinutes,
            'shiftDuration' => $shiftDuration + $variation_33, // Expanded shift duration
            'variation_33' => $variation_33,
            'weekOffDay' => $weekOffDay,
            'shiftSubType' => $shiftSubType,
        ];
    }

    private function getShiftOverride(int $workerId, Carbon $date): ?ShiftOverride
    {
        $shiftOverride = ShiftOverride::where('worker_id', $workerId)
            ->whereDate('start_date', '<=', $date->format('Y-m-d'))
            ->whereDate('end_date', '>=', $date->format('Y-m-d'))
            ->first();

        // if ($date->toDateString() == '2025-09-17') {
        //     dd($shiftOverride);
        // }
        return $shiftOverride;
    }

    private function getManualPunching(int $workerId, Carbon $date): ?ManualPunching
    {
        return ManualPunching::where('worker_id', $workerId)
            ->where('date', $date->format('Y-m-d'))
            ->first();
    }

    /**
     * Reduce by 33%.
     *
     * @param  Carbon  $shiftStart
     * @param  int  $value
     * @return int value
     */
    private function reduceBy33Percent(float $value): float
    {
        return (float) $value * (2 / 3); // Multiply by 2/3 to reduce by 33.3333333%
    }

    /**
     * Reduce by 30.43%.
     *
     * @return float value
     */
    private function reduceBy3043Percent(float $value): float
    {
        return (float) $value - ($value * (30.43 / 100)); // reduce by 30.43%
    }

    /**
     * Get the difference between two Carbon time objects in minutes.
     *
     * @param  Carbon  $startTime  Carbon instance representing the earlier time.
     * @param  Carbon  $endTime  Carbon instance representing the later time.
     * @return float Difference in minutes as a decimal.
     */
    private function getTimeDifference(Carbon $startTime, Carbon $endTime): float
    {
        return $startTime->floatDiffInMinutes($endTime); // Returns the difference in minutes as a float.
    }

    private function processBy8HrsShift($processedData)
    {
        foreach ($processedData as &$data) {

            // $user = Auth::user();
            // if ($user->hasRole('Super Admin')) {
            //     if ($data['date'] <= '2026-05-07') {
            //         dd("Worked");
            //     } else {
            //         dd("Not Worked");
            //     }
            // }
            // Reduce columns by 33%

            $data['shift_start'] = $data['original_shift_start'];
            $data['shift_end'] = $data['original_shift_end'];
            $data['shift_type'] = $data['original_shift_type'];
            $data['shift_hours'] = $data['original_shift_hours'];

            $data['shift_sub_type'] = $data['shift_sub_type'];
            $data['shift_is_internal'] = $data['shift_is_internal'];

            if ($data['shift_is_internal'] == false) {



                if ($data['shift_sub_type'] == 'reduced' && $data['date'] <= '2026-05-07') {
                    // if ($data['shift_sub_type'] == 'reduced') {
                    $data['total_work_hours'] = (int) round($this->reduceBy3043Percent($data['total_work_hours']));
                    $data['work_hours_within_shift'] = (int) round($this->reduceBy3043Percent($data['work_hours_within_shift']));
                    $data['late_coming_minutes'] = (int) round($this->reduceBy3043Percent($data['late_coming_minutes']));
                    $data['early_going_minutes'] = (int) round($this->reduceBy3043Percent($data['early_going_minutes']));
                    $data['adjustable_minutes'] = (int) round($this->reduceBy3043Percent($data['adjustable_minutes']));
                    $data['grace_minutes'] = (int) round($this->reduceBy3043Percent($data['grace_minutes']));

                    $data['late_coming_minutes_original'] = (int) round($this->reduceBy3043Percent($data['late_coming_minutes_original']));
                    $data['early_going_minutes_original'] = (int) round($this->reduceBy3043Percent($data['early_going_minutes_original']));
                } else if ($data['shift_sub_type'] != 'reduced' && $data['date'] <= '2026-05-07') {
                    // } else if ($data['shift_sub_type'] != 'reduced') {
                    $data['total_work_hours'] = (int) round($this->reduceBy33Percent($data['total_work_hours']));
                    $data['work_hours_within_shift'] = (int) round($this->reduceBy33Percent($data['work_hours_within_shift']));
                    $data['late_coming_minutes'] = (int) round($this->reduceBy33Percent($data['late_coming_minutes']));
                    $data['early_going_minutes'] = (int) round($this->reduceBy33Percent($data['early_going_minutes']));
                    $data['adjustable_minutes'] = (int) round($this->reduceBy33Percent($data['adjustable_minutes']));
                    $data['grace_minutes'] = (int) round($this->reduceBy33Percent($data['grace_minutes']));

                    $data['late_coming_minutes_original'] = (int) round($this->reduceBy33Percent($data['late_coming_minutes_original']));
                    $data['early_going_minutes_original'] = (int) round($this->reduceBy33Percent($data['early_going_minutes_original']));
                }
            }

            // dd($data);

            if (isset($data['in_time']) && ! empty($data['in_time'])) {
                if ($data['late_coming_minutes_original'] > 0) {
                    $data['in_time'] = Carbon::parse($data['original_shift_start'])->subMinutes(abs($data['late_coming_minutes_original']))->format('H:i:s');
                } elseif ($data['late_coming_minutes_original'] < 0) {
                    $data['in_time'] = Carbon::parse($data['original_shift_start'])->addMinutes(abs($data['late_coming_minutes_original']))->format('H:i:s');
                } else {
                    $data['in_time'] = $data['original_shift_start'];
                }
            }

            if (isset($data['out_time']) && ! empty($data['out_time'])) {
                if ($data['early_going_minutes_original'] > 0) {
                    $data['out_time'] = Carbon::parse($data['original_shift_end'])->subMinutes(abs($data['early_going_minutes_original']))->format('H:i:s');
                } elseif ($data['early_going_minutes_original'] < 0) {
                    $data['out_time'] = Carbon::parse($data['original_shift_end'])->addMinutes(abs($data['early_going_minutes_original']))->format('H:i:s');
                } else {
                    $data['out_time'] = $data['original_shift_end'];
                }
            }

            if (
                isset($data['in_time']) && ! empty($data['in_time']) &&
                isset($data['in_time_processed']) && ! empty($data['in_time_processed'])
            ) {
                if ($data['late_coming_minutes'] > 0) {
                    $data['in_time_processed'] = Carbon::parse($data['original_shift_start'])->addMinutes($data['late_coming_minutes'])->format('H:i:s');
                } else {
                    $data['in_time_processed'] = $data['original_shift_start'];
                }
            }
            if (
                isset($data['out_time']) && ! empty($data['out_time']) &&
                isset($data['out_time_processed']) && ! empty($data['out_time_processed'])
            ) {
                if ($data['early_going_minutes'] > 0) {
                    $data['out_time_processed'] = Carbon::parse($data['original_shift_end'])->subMinutes($data['early_going_minutes'])->format('H:i:s');
                } else {
                    $data['out_time_processed'] = $data['original_shift_end'];
                }
            }

            unset($data['original_shift_start']);
            unset($data['original_shift_end']);
            unset($data['original_shift_type']);
            unset($data['original_shift_hours']);
            unset($data['late_coming_minutes_original']);
            unset($data['early_going_minutes_original']);
            unset($data['early_going_minutes_original']);
        }

        return $processedData;
    }
}
