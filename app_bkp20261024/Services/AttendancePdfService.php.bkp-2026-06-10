<?php

namespace App\Services;

use App\Models\ProcessedAttendance;
use App\Models\Worker;
use App\Services\ServiceHelpers\WorkerAttendanceServiceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendancePdfService
{

    public function getPdfData(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $workersQuery = Worker::query();

        if ($request->psp && $request->psp != 'all') {
            $workersQuery->where('psp_id', $request->psp);
        }

        if ($request->shift && $request->shift != 'all') {
            $workersQuery->where('shift_id', $request->shift);
        }

        if ($user->hasRole('PSP')) {
            $workersQuery->where('psp_id', $user->psp->id);
        }

        if ($request->employment_status && $request->employment_status == 'Working') {
            $workersQuery->where('employment_status', '!=', 'Left');
        } elseif ($request->employment_status && $request->employment_status != 'Working') {
            $workersQuery->where('employment_status', $request->employment_status);
        } else {
            $workersQuery->where('employment_status', 'Active');
        }

        $ajaxWorkers = $workersQuery->orderBy('code', 'asc')->get();

        $workerEmpcodes = $ajaxWorkers?->pluck('id');

        #Begin:: get from request, if not then current month
        $yearMonth = $request->yearmonth;
        if ($yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
        } else {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
        }
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $datesInMonth = collect();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $datesInMonth->push(Carbon::create($year, $month, $i)->toDateString());
        }

        #End:: get from request, if not then current month
        $attendanceRecords = ProcessedAttendance::whereIn('worker_id', $workerEmpcodes)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'ASC')
            ->get()
            ->groupBy('worker_id');

        $data = $ajaxWorkers->map(function ($worker) use ($attendanceRecords, $daysInMonth) {
            $attendanceRecordsOfThisWorker = $attendanceRecords[$worker->id] ?? false;

            $WorkerAttendanceServiceHelper = new WorkerAttendanceServiceHelper();

            $total_present = $WorkerAttendanceServiceHelper->getPresentCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_halfdays = $WorkerAttendanceServiceHelper->getHalfDayCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_week_off = $WorkerAttendanceServiceHelper->getWeekOffCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_holiday_and_rh = $WorkerAttendanceServiceHelper->getHolidayAndRhCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_missed_punch = $WorkerAttendanceServiceHelper->getMissedPunchCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_absent = $WorkerAttendanceServiceHelper->getAbsentCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_sandwich = $WorkerAttendanceServiceHelper->getSandwichCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_wrong_shifts = $WorkerAttendanceServiceHelper->getWrongShiftCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $total_paid_days = $WorkerAttendanceServiceHelper->getPaidDaysCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

            $totalWorkingDays = $total_present + ($total_halfdays / 2);
            $deduction_days = $total_absent + $total_sandwich;

            $presentOnWeekOff = $WorkerAttendanceServiceHelper->countPresentOnWeekOff($attendanceRecordsOfThisWorker);
            $halfDayOnWeekOff = $WorkerAttendanceServiceHelper->countHalfDayOnWeekOff($attendanceRecordsOfThisWorker);
            $sandwichOnWeekOff = $WorkerAttendanceServiceHelper->countSandwichOnWeekOff($attendanceRecordsOfThisWorker);
            $totalIsWeekOff = $WorkerAttendanceServiceHelper->countTotalIsWeekOff($attendanceRecordsOfThisWorker);

            $newWeekOffCount = $totalIsWeekOff - $sandwichOnWeekOff;
            $newTotalWorkingDays = $totalWorkingDays - ($presentOnWeekOff + $halfDayOnWeekOff / 2); // We are currently not deducting any RH or HL at this point, Santu has promised me that he will help me calculate this, later in Feb 2025 only;
            $newTotalPaidDays = $newTotalWorkingDays + $newWeekOffCount + $total_holiday_and_rh;
            $newDeductionDays = $daysInMonth - $newTotalPaidDays;

            return [
                'worker' => $worker,
                'attendance' => $attendanceRecordsOfThisWorker,
                'summary' => [
                    'days_in_month' => $daysInMonth,
                    'totalWorkingDays' => $totalWorkingDays,
                    'total_week_off' => $total_week_off,
                    'total_holiday_and_rh' => $total_holiday_and_rh,
                    'total_paid_days' => $total_paid_days,
                    'deduction_days' => $deduction_days,
                    'presentOnWeekOff' => ($presentOnWeekOff + $halfDayOnWeekOff / 2),
                    'sandwichOnWeekOff' => $sandwichOnWeekOff,
                    'totalIsWeekOff' => $totalIsWeekOff,
                    'newWeekOffCount' => $newWeekOffCount,
                    'newTotalWorkingDays' => $newTotalWorkingDays,
                    'newTotalPaidDays' => $newTotalPaidDays,
                    'newDeductionDays' => $newDeductionDays,
                ],
            ];
        });

        return $data;
    }
}
