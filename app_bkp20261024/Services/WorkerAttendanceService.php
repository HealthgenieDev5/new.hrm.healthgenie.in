<?php

namespace App\Services;

use App\Interfaces\WorkerAttendanceRepositoryInterface;
use App\Models\Holiday;
use App\Models\ProcessedAttendance;
use App\Services\ServiceHelpers\WorkerAttendanceServiceHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerAttendanceService
{
    /**
     * Create a new class instance.
     */

    /** @var \App\Models\User|null $user */
    private $user;

    public function __construct(private WorkerAttendanceRepositoryInterface $workerAttendanceRepositoryInterface)
    {
        $this->user = Auth::user();
    }

    /**
     * Begin:: Attendance Summary
     */
    public function getMonthlyAttendanceSummaryColumns()
    {

        $columns = [];
        if (!$this->user->hasRole('PSP')) {
            $columns[] = ['data' => 'psp', 'name' => 'psp', 'title' => '<strong ><span class="d-block">PSP</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        }

        $columns[] = ['data' => 'name', 'name' => 'name', 'title' => '<strong ><span class="d-block">Name</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'code', 'name' => 'code', 'title' => '<strong ><span class="d-block">Code</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'joining_date', 'name' => 'joining_date', 'title' => '<strong ><span class="d-block">D.O.J</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];

        $columns[] = ['data' => 'employment_status', 'name' => 'employment_status', 'title' => '<strong ><span class="d-block">Status</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'gender', 'name' => 'gender', 'title' => '<strong ><span class="d-block">Gender</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'shift_type', 'name' => 'shift_type', 'title' => '<strong ><span class="d-block">Shift Type</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'workertype', 'name' => 'workertype', 'title' => '<strong ><span class="d-block">Worker Type</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'designation', 'name' => 'designation', 'title' => '<strong ><span class="d-block">Designation</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'division', 'name' => 'division', 'title' => '<strong ><span class="d-block">Division</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'department', 'name' => 'department', 'title' => '<strong ><span class="d-block">Department</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'subdepartment', 'name' => 'subdepartment', 'title' => '<strong ><span class="d-block">SubDepartment</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'workingarea', 'name' => 'workingarea', 'title' => '<strong ><span class="d-block">WorkingArea</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'productionproduct', 'name' => 'productionproduct', 'title' => '<strong ><span class="d-block">ProductionProduct</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'line', 'name' => 'line', 'title' => '<strong ><span class="d-block">Line</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'process', 'name' => 'process', 'title' => '<strong ><span class="d-block">Process</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];
        $columns[] = ['data' => 'level', 'name' => 'level', 'title' => '<strong ><span class="d-block">Level</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];

        $columns[] = ['data' => 'absent_percentage', 'name' => 'absent_percentage', 'title' => '<strong ><span class="d-block">Absent %</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_absent', 'name' => 'total_absent', 'title' => '<strong ><span class="d-block">A + H/D</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_present_days', 'name' => 'total_present_days', 'title' => '<strong ><span class="d-block">P</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];

        $columns[] = ['data' => 'total_days', 'name' => 'total_days', 'title' => '<strong ><span class="d-block">T Days</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_working_days', 'name' => 'total_working_days', 'title' => '<strong ><span class="d-block">T W Days</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_weekoff_and_holidays', 'name' => 'total_weekoff_and_holidays', 'title' => '<strong ><span class="d-block">W/O + HL</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_half_days', 'name' => 'total_half_days', 'title' => '<strong ><span class="d-block">HD</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_el', 'name' => 'total_el', 'title' => '<strong ><span class="d-block">EL</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_cl', 'name' => 'total_cl', 'title' => '<strong ><span class="d-block">CL</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'total_missed_punch', 'name' => 'total_missed_punch', 'title' => '<strong ><span class="d-block">M/P</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'last_present_date', 'name' => 'last_present_date', 'title' => '<strong ><span class="d-block">LPD</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'];
        $columns[] = ['data' => 'date_of_leaving', 'name' => 'date_of_leaving', 'title' => '<strong ><span class="d-block">D.O.L</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'];


        return $columns;
    }

    public function getMonthlyAttendanceSummary(Request $request)
    {
        $workers = $this->workerAttendanceRepositoryInterface->getMonthlyAttendanceSummaryWorkers($request);

        $date_range = $request->input('filter_date_range') ?? date('Y-m-01') . ' to ' . date('Y-m-d');
        $date_range_array = explode("to", $date_range);
        if (!isset($date_range_array[1])) {
            $date_range_array[1] = $date_range_array[0];
        }
        $startDate = Carbon::parse($date_range_array[0])->startOfDay();
        $endDate = Carbon::parse($date_range_array[1])->startOfDay();
        $groupedByWorker = $this->workerAttendanceRepositoryInterface->getMonthlyAttendanceSummaryProcessedAttendance($startDate, $endDate);

        $summaryData = [];

        foreach ($workers as $worker) {

            $records = $groupedByWorker->get($worker->id, collect());

            $cleanStart = $startDate->copy()->startOfDay();
            $cleanEnd   = $endDate->copy()->startOfDay();

            $dateOfJoining = $worker->date_of_joining ? Carbon::parse($worker->date_of_joining)->startOfDay() : null;
            $dateOfLeaving = $worker->date_of_leaving ? Carbon::parse($worker->date_of_leaving)->startOfDay() : null;
            if (
                ($dateOfJoining && $dateOfJoining->gt($cleanEnd)) ||
                ($dateOfLeaving && $dateOfLeaving->lt($cleanStart))
            ) {
                $totalDays = 0;
                $records = null;
            } else {
                $finalStart = $dateOfJoining && $dateOfJoining->gt($cleanStart) ? $dateOfJoining : $cleanStart;
                $finalEnd = $dateOfLeaving && $dateOfLeaving->lt($cleanEnd) ? $dateOfLeaving : $cleanEnd;
                if ($finalEnd->lt($finalStart)) {
                    $totalDays = 0;
                    $records = null;
                } else {
                    $totalDays = $finalStart->diffInDays($finalEnd) + 1;
                    $records = $records->filter(function ($record) use ($finalStart, $finalEnd) {
                        // Make sure $record->date is a Carbon instance
                        return $record->date->between($finalStart, $finalEnd);
                    });
                }
            }



            $totalWorkingDays = $this->getMonthlyAttendanceSummaryTotalWorkingDays($records);

            $totalPresentDays = $this->getMonthlyAttendanceSummaryTotalPresentDays($records);

            $totalHalfDays = ($records?->where('status', 'H/D')->count() / 2);

            $totalEl          = $records?->where('status', 'EL')->count() ?? 0;

            $totalCl          = $records?->where('status', 'CL')->count() ?? 0;

            $totalWeekOff     = $records?->where('status', 'W/O')->count() ?? 0;

            // $totalHL          = $records?->where('status', 'HL')->count() ?? 0;
            // $totalHL          += $records?->where('status', 'NH')->count() ?? 0;
            // $totalHL          += $records?->where('status', 'SPL HL')->count() ?? 0;
            $totalHL = $records?->whereIn('status', ['HL', 'SPL HL', 'NH'])->count() ?? 0;


            $totalMissedPunch     = $records?->where('status', 'M/P')
                ->reject(fn($record) => $record->date == Carbon::today())
                ->count() ?? 0;

            $totalAbsent      = $records?->where('status', 'A')->count()
                + $records?->where('status', 'S/W')->count()
                // + $records->where('status', 'W/O')->count()
                + $records?->where('status', 'WRONG SHIFT')->count()
                + ($records?->where('status', 'H/D')->count() / 2);

            $lastPresentRecord = $records?->whereIn('status', ['P', 'H/D', 'M/P'])->sortBy('date')->last();
            if (!$lastPresentRecord) {
                $lastPresentRecord = ProcessedAttendance::where('worker_id', $worker->id)
                    ->where('date', '<=', $endDate->format('Y-m-d'))
                    ->whereIn('status', ['P', 'H/D', 'M/P'])
                    ->orderBy('date', 'desc')->first();
            }

            $lastPresentDate = $lastPresentRecord ? $lastPresentRecord->date : null;

            $summaryData[] = [
                'psp'                => $worker?->psp?->name,
                'name'               => $worker->name,
                'code'               => $worker->code,
                'employment_status'  => $worker->employment_status,
                'gender'             => $worker->gender,
                'shift_type'         => $worker?->shift?->shift_type,
                'workertype'         => $worker->workertype?->name,
                'designation'        => $worker->designation?->name,
                'division'           => $worker->division?->name,
                'department'         => $worker->department?->name,
                'subdepartment'      => $worker->subdepartment?->name,
                'workingarea'        => $worker->workingarea?->name,
                'productionproduct'  => $worker->productionproduct?->name,
                'line'               => $worker->line?->name,
                'process'            => $worker->process?->name,
                'level'              => $worker->level?->name,

                'absent_percentage'  => $totalWorkingDays > 0 ? round(($totalAbsent / $totalWorkingDays) * 100) . "%" : "100%",
                // 'absent_percentage'  => round(($totalAbsent / $totalWorkingDays) * 100) . "%",
                'total_days'         => $totalDays,
                'total_working_days' => $totalWorkingDays,
                'total_present_days' => $totalPresentDays,
                'total_weekoff_and_holidays' => $totalWeekOff + $totalHL,
                'total_half_days'    => $totalHalfDays,
                'total_el'           => $totalEl,
                'total_cl'           => $totalCl,
                'total_missed_punch' => $totalMissedPunch,
                'total_absent'       => $totalAbsent,
                'last_present_date' => $lastPresentDate
                    ? $lastPresentDate->format('Y-m-d')
                    : null,
                'joining_date' => $worker?->date_of_joining
                    ? Carbon::parse($worker->date_of_joining)->format("Y-m-d")
                    : null,
                'date_of_leaving' => $worker?->date_of_leaving
                    ? Carbon::parse($worker->date_of_leaving)->format("Y-m-d")
                    : null
            ];
        }

        return $summaryData;
    }

    private function getMonthlyAttendanceSummaryTotalWorkingDays($records)
    {
        $totalWorkingDays = 0;
        if ($records) {
            foreach ($records as $record) {
                if (!$record['is_weekoff'] && !$record['is_holiday']) {
                    $totalWorkingDays++;
                }
            }
        }
        return $totalWorkingDays;
    }

    private function getMonthlyAttendanceSummaryTotalPresentDays($records)
    {
        $totalPresentDays = 0;
        if ($records) {
            foreach ($records as $record) {
                if ($record['status'] == 'P') {
                    $totalPresentDays = $totalPresentDays + 1;
                }
                // if ($record['status'] == 'H/D') {
                //     $totalPresentDays = $totalPresentDays + 0.5;
                // }
                if ($record['status'] == 'M/P' && $record['date']->format('Y-m-d') == date('Y-m-d')) {
                    $totalPresentDays = $totalPresentDays + 1;
                }
            }
        }
        return $totalPresentDays;
    }
    /**
     * End:: Attendance Summary
     */



    /**
     * Begin:: Time Attendance
     */
    public function getTimeAttendanceDatesInMonth(Request $request)
    {
        #Initialize variables
        $yearMonth = $request->input('yearmonth');
        if ($yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
        } else {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $yearMonth = $year . '-' . $month;
        }
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $datesInMonth = collect();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $datesInMonth->push(Carbon::create($year, $month, $i)->toDateString());
        }
        return $datesInMonth;
    }

    public function generateTimeAttendanceColumns(Request $request)
    {
        $columns = [];
        $columns[] = ['data' => 'code', 'name' => 'Worker Code<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'name', 'name' => 'Worker Name<br><br>', 'searchable' => true];

        if (!$this->user->hasRole('PSP')) {
            $columns[] = ['data' => 'column_name', 'name' => 'Column<br><br>', 'searchable' => true];
        }
        $datesInMonth = $this->getTimeAttendanceDatesInMonth($request);
        foreach ($datesInMonth as $date) {
            $day = Carbon::parse($date)->format('D');
            $dateText = Carbon::parse($date)->format('d M');
            if ($day == "Sun") {
                $classes = "text-center text-white bg-danger text-hover-primary px-2 ";
            } elseif ($date === Carbon::now()) {
                $classes = "text-center text-white bg-primary text-hover-primary px-2 ";
            } else {
                $classes = "text-center text-dark text-hover-primary px-2 ";
            }

            $dateHtml = "<div class='{$classes}'>{$dateText}<br>{$day}</div>";
            $columns[] = ['data' => $date, 'name' => $dateHtml, 'searchable' => true];
        }

        $columns[] = ['data' => 'total_late_coming', 'name' => '<div class="text-center text-dark text-hover-primary px-2" style="background-color: rgb(255, 198, 212)">Total Late<br><br></div>', 'searchable' => true];
        $columns[] = ['data' => 'total_early_going', 'name' => '<div class="text-center text-dark text-hover-primary px-2" style="background-color: rgb(255, 198, 212)">Total Early<br><br></div>', 'searchable' => true];
        $columns[] = ['data' => 'total_grace', 'name' => '<div class="text-center text-dark text-hover-primary px-2" style="background-color: rgb(175, 255, 211)">Total Grace<br><br></div>', 'searchable' => true];

        $columns[] = ['data' => 'total_present', 'name' => 'Total Present<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_halfdays', 'name' => 'Total Half Day<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_week_off', 'name' => 'Total Week Off<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_holiday_and_rh', 'name' => 'Total Holiday + RH<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_missed_punch', 'name' => 'Total Missed Punch<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_absent', 'name' => 'Total Absent<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_sandwich', 'name' => 'Total Sandwich<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_wrong_shifts', 'name' => 'Total Wrong Shifts<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_paid_days', 'name' => 'Total Paid Days<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'total_present_on_week_off', 'name' => 'Total Present on W/O<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'days_in_month', 'name' => 'Month Days<br><br>', 'searchable' => true];
        $columns[] = ['data' => 'searchable_name', 'name' => 'searchable_name', 'visible' => false, 'searchable' => true];

        return $columns;
    }

    public function getMonthlyAttendance(Request $request)
    {
        $datesInMonth = $this->getTimeAttendanceDatesInMonth($request);
        $ajaxWorkers = $this->workerAttendanceRepositoryInterface->getTimeAttendanceWorkers($request);
        $user = $this->user;
        $data = $ajaxWorkers->flatMap(function ($worker) use ($datesInMonth, $user) {
            return $this->transformWorkerData($worker, $datesInMonth, $user);
        });
        return $data;
    }

    private function transformWorkerData($worker, $datesInMonth, $user)
    {
        $workerAttendanceServiceHelper = new WorkerAttendanceServiceHelper();

        $date_of_joining = $worker->date_of_joining ? Carbon::parse($worker->date_of_joining)->format('d M Y') : 'N/A';
        $date_of_joining_html = "<br><small style='font-size: 0.65rem'>Joined: {$date_of_joining}</small>";
        $attendanceRecordsOfThisWorker = $worker->processedAttendance ?? false;
        $firstDate = $datesInMonth->first();

        // dd($attendanceRecordsOfThisWorker);

        $daysInMonth = Carbon::parse($firstDate)->daysInMonth;

        $presentWithWeekOff = $workerAttendanceServiceHelper->getPresentCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);
        $halfDayWithWeekOff = $workerAttendanceServiceHelper->getHalfDayCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

        $absentWithWeekOffAbsent = $workerAttendanceServiceHelper->getAbsentCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

        $presentOnWeekOff = $workerAttendanceServiceHelper->countPresentOnWeekOff($attendanceRecordsOfThisWorker);
        $halfDayOnWeekOff = $workerAttendanceServiceHelper->countHalfDayOnWeekOff($attendanceRecordsOfThisWorker);

        $newTotalPresentDays = $presentWithWeekOff - $presentOnWeekOff; #Ok
        $newTotalHalfDays = $halfDayWithWeekOff - $halfDayOnWeekOff; #Ok

        $newTotalAbsent = $absentWithWeekOffAbsent - ($halfDayOnWeekOff / 2);
        $sandwichOnWeekOff = $workerAttendanceServiceHelper->countSandwichOnWeekOff($attendanceRecordsOfThisWorker);
        $totalIsWeekOff = $workerAttendanceServiceHelper->countTotalIsWeekOff($attendanceRecordsOfThisWorker);

        $newTotalWeekOff = $totalIsWeekOff - $sandwichOnWeekOff; #OK

        $totalHolidayAndRh = $workerAttendanceServiceHelper->getHolidayAndRhCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []);

        $newTotalPaidDays = $newTotalPresentDays + ($newTotalHalfDays / 2) + $newTotalWeekOff + $totalHolidayAndRh;

        $newDeductionDays = $daysInMonth - $newTotalPaidDays;


        $totalLateComing = $workerAttendanceServiceHelper->getTotalLateComing($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('late_coming_minutes')->toArray() : []);
        $totalEarlyGoing = $workerAttendanceServiceHelper->getTotalEarlyGoing($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('early_going_minutes')->toArray() : []);
        $totalGrace = $workerAttendanceServiceHelper->getTotalGrace($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker?->pluck('grace_minutes')->toArray() : []);


        $mainRow = [
            'code' => $worker->code,
            'name' => $user->hasRole('PSP') ? $worker->name . $date_of_joining_html : $worker->name,
            'total_late_coming' => $totalLateComing,
            'total_early_going' => $totalEarlyGoing,
            'total_grace' => $totalGrace,

            'total_present' => $newTotalPresentDays,
            'total_halfdays' => $newTotalHalfDays,
            'total_week_off' => $newTotalWeekOff,
            'total_holiday_and_rh' => $totalHolidayAndRh,
            'total_missed_punch' => $workerAttendanceServiceHelper->getMissedPunchCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []),
            'total_absent' => $newTotalAbsent,
            'total_sandwich' => $sandwichOnWeekOff,
            'total_wrong_shifts' => $workerAttendanceServiceHelper->getWrongShiftCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->pluck('status')->toArray() : []),
            'total_paid_days' => $newTotalPaidDays,
            'total_present_on_week_off' => $presentOnWeekOff + ($halfDayOnWeekOff / 2),
            'days_in_month' => $daysInMonth,
        ];

        foreach ($datesInMonth as $date) {
            $mainRow[$date] = $workerAttendanceServiceHelper->getFirstRowHtml($attendanceRecordsOfThisWorker, $date, $user);
        }

        $mainRow['searchable_name'] = $worker->name . "|" . $worker->code;

        if ($user->hasRole('PSP')) {
            return [$mainRow];
        }

        # If not a psp
        $mainRow['column_name'] = "Shift Start";

        #Second Row
        $secondRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];

        $date_of_joining = $worker->date_of_joining ? Carbon::parse($worker->date_of_joining)->format('d M Y') : 'N/A';
        $date_of_joining_html = "<small style='font-size: 0.65rem'>Joined: {$date_of_joining}</small>";
        $secondRow["name"] = $date_of_joining_html;

        $secondRow['column_name'] = "Shift End";
        foreach ($datesInMonth as $date) {
            $secondRow[$date] = $workerAttendanceServiceHelper->getSecondRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $secondRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Third Row
        $thirdRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $thirdRow['column_name'] = "Punch In";
        foreach ($datesInMonth as $date) {
            $thirdRow[$date] = $workerAttendanceServiceHelper->getThirdRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $thirdRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Fourth Row
        $fourthRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $fourthRow['column_name'] = "Punch Out";
        foreach ($datesInMonth as $date) {
            $fourthRow[$date] = $workerAttendanceServiceHelper->getFourthRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $fourthRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Fifth Row
        $fifthRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $fifthRow['column_name'] = "Status";
        foreach ($datesInMonth as $date) {
            $fifthRow[$date] = $workerAttendanceServiceHelper->getFifthRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $fifthRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Sixth Row
        $sixthRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $sixthRow['column_name'] = "Remarks";
        foreach ($datesInMonth as $date) {
            $sixthRow[$date] = $workerAttendanceServiceHelper->getSixthRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $sixthRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Seventh Row
        $seventhRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $seventhRow['column_name'] = "Late Coming";
        foreach ($datesInMonth as $date) {
            $seventhRow[$date] = $workerAttendanceServiceHelper->getSeventhRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $seventhRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Eightth Row
        $eightthRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $eightthRow['column_name'] = "Early Going";
        foreach ($datesInMonth as $date) {
            $eightthRow[$date] = $workerAttendanceServiceHelper->getEightthRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $eightthRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Nineth Row
        $ninethRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $ninethRow['column_name'] = "Daily Grace";
        foreach ($datesInMonth as $date) {
            $ninethRow[$date] = $workerAttendanceServiceHelper->getNinethRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $ninethRow['searchable_name'] = $worker->name . "|" . $worker->code;





        #Tenth Row
        $tenthRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $tenthRow['column_name'] = "Punch In Original";
        foreach ($datesInMonth as $date) {
            $tenthRow[$date] = $workerAttendanceServiceHelper->getTenthRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $tenthRow['searchable_name'] = $worker->name . "|" . $worker->code;

        #Eleventh Row
        $eleventhRow = ['code' => "", 'name' => "", 'total_late_coming' => "", 'total_early_going' => "", 'total_grace' => "", 'total_present' => "", 'total_halfdays' => "", 'total_week_off' => "", 'total_holiday_and_rh' => "", 'total_missed_punch' => "", 'total_absent' => "", 'total_sandwich' => "", 'total_wrong_shifts' => "", 'total_paid_days' => "", 'total_present_on_week_off' => "", 'days_in_month' => ""];
        $eleventhRow['column_name'] = "Punch Out Original";
        foreach ($datesInMonth as $date) {
            $eleventhRow[$date] = $workerAttendanceServiceHelper->getEleventhRowHtml($attendanceRecordsOfThisWorker, $date);
        }
        $eleventhRow['searchable_name'] = $worker->name . "|" . $worker->code;





        // return [$mainRow, $secondRow, $thirdRow, $fourthRow, $fifthRow, $sixthRow, $seventhRow, $eightthRow, $ninethRow, $tenthRow, $eleventhRow];
        return [$mainRow, $secondRow, $thirdRow, $fourthRow, $fifthRow, $sixthRow, $seventhRow, $eightthRow, $ninethRow];
    }
    /**
     * End:: Time Attendance
     */


    /**
     * Begin:: Continuous Absent
     */
    public function getContinuousAbsentColumns()
    {

        $columns = [];

        // $columns[] = [
        //     'data' => 'working_days_lm',
        //     'name' => 'working_days_lm',
        //     'title' => '<strong ><span class="d-block">Working Days</span><span class="d-block" style="font-size:0.6rem">LM</span></strong>'
        // ];
        $columns[] = [
            'data' => 'lm_present',
            'name' => 'lm_present',
            'title' => '<strong ><span class="d-block">Present</span><span class="d-block" style="font-size:0.6rem">LM</span></strong>'
        ];
        $columns[] = [
            'data' => 'lm_absent',
            'name' => 'lm_absent',
            'title' => '<strong ><span class="d-block">Absent</span><span class="d-block" style="font-size:0.6rem">LM</span></strong>'
        ];
        $columns[] = [
            'data' => 'lm_leave',
            'name' => 'lm_leave',
            'title' => '<strong ><span class="d-block">Leave</span><span class="d-block" style="font-size:0.6rem">LM</span></strong>'
        ];
        $columns[] = [
            'data' => 'lm_absent_percent',
            'name' => 'lm_absent_percent',
            'title' => '<strong ><span class="d-block">Absent %</span><span class="d-block" style="font-size:0.6rem">LM</span></strong>'
        ];
        $columns[] = [
            'data' => 'lm_off_percent',
            'name' => 'lm_off_percent',
            'title' => '<strong ><span class="d-block">Off %</span><span class="d-block" style="font-size:0.6rem">LM</span></strong>'
        ];

        // $columns[] = [
        //     'data' => 'working_days_mtd',
        //     'name' => 'working_days_mtd',
        //     'title' => '<strong ><span class="d-block">Working Days</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'
        // ];
        $columns[] = [
            'data' => 'mtd_present',
            'name' => 'mtd_present',
            'title' => '<strong ><span class="d-block">Present</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'
        ];
        $columns[] = [
            'data' => 'mtd_absent',
            'name' => 'mtd_absent',
            'title' => '<strong ><span class="d-block">Absent</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'
        ];
        $columns[] = [
            'data' => 'mtd_leave',
            'name' => 'mtd_leave',
            'title' => '<strong ><span class="d-block">Leave</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'
        ];
        $columns[] = [
            'data' => 'mtd_absent_percent',
            'name' => 'mtd_absent_percent',
            'title' => '<strong ><span class="d-block">Absent %</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'
        ];
        $columns[] = [
            'data' => 'mtd_off_percent',
            'name' => 'mtd_off_percent',
            'title' => '<strong ><span class="d-block">Off %</span><span class="d-block" style="font-size:0.6rem">MTD</span></strong>'
        ];

        $columns[] = [
            'data' => 'date_of_joining',
            'name' => 'date_of_joining',
            'title' => '<strong ><span class="d-block">DOJ</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'code',
            'name' => 'code',
            'title' => '<strong ><span class="d-block">Code</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'name',
            'name' => 'name',
            'title' => '<strong ><span class="d-block">Name</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'psp',
            'name' => 'psp',
            'title' => '<strong ><span class="d-block">PSP</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'employment_status',
            'name' => 'employment_status',
            'title' => '<strong ><span class="d-block">Status</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'shift',
            'name' => 'shift',
            'title' => '<strong ><span class="d-block">Shift</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'designation',
            'name' => 'designation',
            'title' => '<strong ><span class="d-block">Designation</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'gender',
            'name' => 'gender',
            'title' => '<strong ><span class="d-block">Gender</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'division',
            'name' => 'division',
            'title' => '<strong ><span class="d-block">Division</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'department',
            'name' => 'department',
            'title' => '<strong ><span class="d-block">Department</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'subdepartment',
            'name' => 'subdepartment',
            'title' => '<strong ><span class="d-block">SubDepartment</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'workingarea',
            'name' => 'workingarea',
            'title' => '<strong ><span class="d-block">WorkingArea</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'productionproduct',
            'name' => 'productionproduct',
            'title' => '<strong ><span class="d-block">ProductionProduct</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'line',
            'name' => 'line',
            'title' => '<strong ><span class="d-block">Line</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'process',
            'name' => 'process',
            'title' => '<strong ><span class="d-block">Process</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'level',
            'name' => 'level',
            'title' => '<strong ><span class="d-block">Level</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];
        $columns[] = [
            'data' => 'workertype',
            'name' => 'workertype',
            'title' => '<strong ><span class="d-block">Worker Type</span><span class="d-block" style="font-size:0.6rem">&nbsp;</span></strong>'
        ];

        return $columns;
    }

    public function getContinuousAbsent(Request $request)
    {

        $today = Carbon::today();
        $today = $today->copy()->startOfDay();

        $threeDaysAgo = $today->copy()->subDays(2);
        $threeDaysAgo = $threeDaysAgo->copy()->startOfDay();

        $startOfLM = $today->copy()->subMonth()->startOfMonth();
        $startOfLM = $startOfLM->copy()->startOfDay();

        $endOfLM = $today->copy()->subMonth()->endOfMonth();
        $endOfLM = $endOfLM->copy()->startOfDay();

        $startOfMTD = $today->copy()->startOfMonth();
        $startOfMTD = $startOfMTD->copy()->startOfDay();

        $holidays = Holiday::pluck('date')->toArray();
        $weekOffDays = ['Sunday'];

        $absentWorkerIds = $this->workerAttendanceRepositoryInterface->getContinuousAbsentWorkerIds($threeDaysAgo, $today, $request);
        // return $absentWorkerIds;

        $attendanceSummary = $this->workerAttendanceRepositoryInterface->getContinuousAbsentAttendanceSummary($startOfLM, $endOfLM, $startOfMTD, $today, $absentWorkerIds);

        // dd($attendanceSummary);
        // return $attendanceSummary;

        $workers = $this->workerAttendanceRepositoryInterface->getContinuousAbsentWorkerWithDetail($absentWorkerIds);
        // return $workers;

        $result = $workers->map(function ($worker) use ($attendanceSummary, $startOfLM, $endOfLM, $startOfMTD, $today, $holidays, $weekOffDays) {
            $dateOfJoining = $worker->date_of_joining ? Carbon::parse($worker->date_of_joining) : null;
            $dateOfLeaving = $worker->date_of_leaving ? Carbon::parse($worker->date_of_leaving) : null;
            $workingDaysLM = $this->getWorkingDaysExcludingOffs($startOfLM, $endOfLM, $dateOfJoining, $dateOfLeaving, $holidays, $weekOffDays);
            $workingDaysMTD = $this->getWorkingDaysExcludingOffs($startOfMTD, $today, $dateOfJoining, $dateOfLeaving, $holidays, $weekOffDays);
            $a = $attendanceSummary[$worker->id] ?? (object)[
                'lm_present' => 0,
                'lm_absent' => 0,
                'lm_leave' => 0,
                'mtd_present' => 0,
                'mtd_absent' => 0,
                'mtd_leave' => 0
            ];

            // $lm_total = $a->lm_present + $a->lm_absent + $a->lm_leave;
            // $mtd_total = $a->mtd_present + $a->mtd_absent + $a->mtd_leave;

            return [
                'id' => $worker->id,
                'date_of_joining' => $worker->date_of_joining ? Carbon::parse($worker->date_of_joining)->format('d M Y') : '',
                'code' => $worker->code,
                'name' => $worker->name,
                'psp' => $worker->psp?->name,
                'employment_status' => $worker->employment_status,
                'shift' => $worker->shift?->name,
                'gender' => $worker?->gender ? ucfirst($worker?->gender) : '',
                'designation' => $worker->designation?->name ?? null,
                'division' => $worker->division?->name ?? null,
                'department' => $worker->department?->name ?? null,
                'subdepartment' => $worker->subdepartment?->name ?? null,
                'line' => $worker->line?->name ?? null,
                'process' => $worker->process?->name ?? null,
                'level' => $worker->level?->name ?? null,
                'workingarea' => $worker->workingArea?->name,
                'productionproduct' => $worker->productionProduct?->name,
                'workertype' => $worker->workertype?->name,

                'working_days_lm' => $workingDaysLM,
                'lm_present' => $a->lm_present,
                'lm_absent' => $a->lm_absent,
                'lm_leave' => $a->lm_leave,
                'lm_absent_percent' => $workingDaysLM > 0 ? round(($a->lm_absent / $workingDaysLM) * 100, 2) . "%" : '0' . "%",
                'lm_off_percent' => $workingDaysLM > 0 ? round((($a->lm_absent + $a->lm_leave) / $workingDaysLM) * 100, 2) . "%" : '0' . "%",

                'working_days_mtd' => $workingDaysMTD,
                'mtd_present' => $a->mtd_present,
                'mtd_absent' => $a->mtd_absent,
                'mtd_leave' => $a->mtd_leave,
                'mtd_absent_percent' => $workingDaysMTD > 0 ? round(($a->mtd_absent / $workingDaysMTD) * 100, 2) . "%" : '0' . "%",
                'mtd_off_percent' => $workingDaysMTD > 0 ? round((($a->mtd_absent + $a->mtd_leave) / $workingDaysMTD) * 100, 2) . "%" : '0' . "%",
            ];
        });

        return $result;
    }

    public function getWorkingDaysExcludingOffs(Carbon $monthStart, Carbon $monthEnd, ?Carbon $joining, ?Carbon $leaving, array $holidays = [], array $weekOffDays = ['Sunday']): int
    {
        // Adjust start and end based on joining/leaving
        $start = $joining && $joining->gt($monthStart) ? $joining->copy() : $monthStart->copy();
        $end = $leaving && $leaving->lt($monthEnd) ? $leaving->copy() : $monthEnd->copy();

        $start = $joining && $joining->gt($monthStart) ? $joining->copy() : $monthStart->copy();
        $end = $leaving && $leaving->lt($monthEnd) ? $leaving->copy() : $monthEnd->copy();

        if ($start->gt($end)) {
            return 0;
        }

        // Prepare a set of holiday dates (Y-m-d format)
        $holidayDates = collect($holidays)->map(fn($h) => Carbon::parse($h)->toDateString())->toArray();

        $period = CarbonPeriod::create($start, $end);
        $count = 0;

        foreach ($period as $date) {
            $dayName = $date->format('l'); // e.g. Monday
            $dateStr = $date->toDateString();

            if (!in_array($dayName, $weekOffDays) && !in_array($dateStr, $holidayDates)) {
                $count++;
            }
        }

        return $count;
    }
    /**
     * End:: Continuous Absent
     */
}
