<?php

namespace App\Services;

use App\Models\ProcessedAttendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class TenureBonusService
{
    /**
     * Calculates the actual date a worker becomes eligible after a certain number of working days.
     *
     * @param int $numberOfWorkingDays The number of working days required by the rule.
     * @return Carbon The actual date of eligibility.
     */



    public function calculateEligibilityActualDate(Worker $worker, int $numberOfPaidDaysRequired): ?Carbon
    {

        $ruleStartDate = Carbon::parse('2025-06-01');
        $joiningDate = Carbon::parse($worker->date_of_joining);

        // Log::warning("Worker ID {$worker->id} joining date {$joiningDate->toDateString()} and rule start date {$ruleStartDate->toDateString()}");


        $dateIterator = $joiningDate->greaterThan($ruleStartDate)
            ? $joiningDate->copy()
            : $ruleStartDate->copy();

        $paidDaysCounted = 0.0;

        //$maxIterations = Carbon::parse($dateIterator)->diffInDays(Carbon::today()->startOfDay()) + 1;

        $maxIterations = Carbon::parse($dateIterator)->diffInDays(Carbon::today()) + 1;

        // Log::warning(" starting from {$dateIterator->toDateString()}");


        $iterations = 0;

        while ($paidDaysCounted < $numberOfPaidDaysRequired && $iterations < $maxIterations) {
            $attendanceRecord = ProcessedAttendance::where('worker_id', $worker->id)
                ->whereDate('date', $dateIterator->toDateString())
                ->first();
            $dayValue = 0.0;
            if ($attendanceRecord && $attendanceRecord->status) {
                $status = strtoupper(trim($attendanceRecord->status));

                switch ($status) {
                    case 'P':   // Present
                        //    case 'W/O': // Week Off
                        //    case 'EL':  // Earned Leave
                        //   case 'CL':  // Casual Leave
                        $dayValue = 1.0;
                        break;
                    case 'H/D': // Half Day
                        $dayValue = 0.5;
                        break;
                        // Other statuses like 'A' (Absent), 'L' (Late), etc., will result in $dayValue = 0.0
                }
            }

            $paidDaysCounted += $dayValue;
            // echo ("worker_id: {$worker->id}, date: {$dateIterator->toDateString()}, status: {$status}, dayValue: {$dayValue}, paidDaysCounted: {$paidDaysCounted}, numberOfPaidDaysRequired: {$numberOfPaidDaysRequired}\n");
            $dateIterator->addDay();
            $iterations++;
            if ($iterations >= $maxIterations) {
                Log::warning("Max iterations reached for worker ID {$worker->id} on date {$dateIterator->toDateString()}");
                break;
            }
        }

        if ($paidDaysCounted >= $numberOfPaidDaysRequired && $paidDaysCounted <= ($numberOfPaidDaysRequired + 0.5)) {
            return $dateIterator->copy(); // This is the date when eligibility is met
        } else {
            Log::warning("Worker ID {$worker->id} did not meet the required paid days. Paid Days Counted: {$paidDaysCounted}, Required: {$numberOfPaidDaysRequired}");
            return null;
        }
    }
}
