<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\ProcessedAttendance;
use App\Models\Psp;
use App\Models\ShiftOverride;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSummaryService
{
    private $user;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }

    public function generateColumns($yearMonth)
    {
        $startDate = Carbon::parse($yearMonth . '-01')->startOfMonth();
        $endDate = Carbon::parse($yearMonth . '-01')->endOfMonth();
        if ($yearMonth === date('Y-m')) {
            $endDate = Carbon::today();
        }

        $columns = [
            'PSP',
            'Status',
            'Max',
            'Avg',
        ];


        for ($day = 1; $day <= $endDate->day; $day++) {
            $columns[] = $yearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
        }

        return $columns;
    }

    private function getShiftOverride(int $workerId, Carbon $date): ?ShiftOverride
    {
        return ShiftOverride::where('worker_id', $workerId)
            ->whereDate('start_date', '<=', $date->format('Y-m-d'))
            ->whereDate('end_date', '>=', $date->format('Y-m-d'))
            ->first();
    }

    public function countActiveDayNight($pspId, $dateString)
    {
        $date = Carbon::parse($dateString)->startOfDay();

        // Get all active workers based on your logic
        $workers = Worker::where('psp_id', $pspId)
            ->where(function ($query) use ($date) {
                $query->whereNull('date_of_leaving')
                    ->orWhere('date_of_leaving', '>=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('date_of_joining')
                    ->orWhere('date_of_joining', '<=', $date);
            })
            ->with('shift')
            ->get();

        $totalDay   = 0;
        $totalNight = 0;

        foreach ($workers as $worker) {
            // 1) Check if there’s a shift override for this worker
            $override = $this->getShiftOverride($worker->id, $date);

            // 2) If override exists, use that shift’s type.
            //    Otherwise, use the worker’s default shift->type
            if ($override) {
                // If you store the override shift type in a column directly:
                $shiftType = $override->shift->shift_type; // e.g., 'day' or 'night'

                // Or if you store a shift_id in ShiftOverride and need to fetch:
                // $overrideShift = $override->shift; // if there's a shift relation
                // $shiftType = $overrideShift->type;
            } else {
                // fallback to worker's default shift->type
                $shiftType = $worker->shift?->shift_type; // e.g. 'day' or 'night'
            }

            // 3) Increment counters
            if ($shiftType === 'day') {
                $totalDay++;
            } elseif ($shiftType === 'night') {
                $totalNight++;
            }
        }

        // Return or do whatever you want with these counts
        return [
            'total_day'   => $totalDay,
            'total_night' => $totalNight,
        ];
    }

    public function countLeftDayNight($pspId, $dateString)
    {
        $date = Carbon::parse($dateString)->startOfDay();
        $workers = Worker::where('psp_id', $pspId)
            ->whereDate('date_of_leaving', $date)
            ->with('shift')
            ->get();

        $totalDay   = 0;
        $totalNight = 0;

        foreach ($workers as $worker) {
            // 1) Check if there’s a shift override for this worker
            $override = $this->getShiftOverride($worker->id, $date);

            // 2) If override exists, use that shift’s type.
            //    Otherwise, use the worker’s default shift->type
            if ($override) {
                // If you store the override shift type in a column directly:
                $shiftType = $override->shift->shift_type; // e.g., 'day' or 'night'

                // Or if you store a shift_id in ShiftOverride and need to fetch:
                // $overrideShift = $override->shift; // if there's a shift relation
                // $shiftType = $overrideShift->type;
            } else {
                // fallback to worker's default shift->type
                $shiftType = $worker->shift?->shift_type; // e.g. 'day' or 'night'
            }

            // 3) Increment counters
            if ($shiftType === 'day') {
                $totalDay++;
            } elseif ($shiftType === 'night') {
                $totalNight++;
            }
        }

        // Return or do whatever you want with these counts
        return [
            'total_day'   => $totalDay,
            'total_night' => $totalNight,
        ];
    }

    public function countJoinedDayNight($pspId, $dateString)
    {
        $date = Carbon::parse($dateString)->startOfDay();
        $workers = Worker::where('psp_id', $pspId)
            ->whereDate('date_of_joining', $date)
            ->with('shift')
            ->get();

        $totalDay   = 0;
        $totalNight = 0;

        foreach ($workers as $worker) {
            // 1) Check if there’s a shift override for this worker
            $override = $this->getShiftOverride($worker->id, $date);

            // 2) If override exists, use that shift’s type.
            //    Otherwise, use the worker’s default shift->type
            if ($override) {
                // If you store the override shift type in a column directly:
                $shiftType = $override->shift->shift_type; // e.g., 'day' or 'night'

                // Or if you store a shift_id in ShiftOverride and need to fetch:
                // $overrideShift = $override->shift; // if there's a shift relation
                // $shiftType = $overrideShift->type;
            } else {
                // fallback to worker's default shift->type
                $shiftType = $worker->shift?->shift_type; // e.g. 'day' or 'night'
            }

            // 3) Increment counters
            if ($shiftType === 'day') {
                $totalDay++;
            } elseif ($shiftType === 'night') {
                $totalNight++;
            }
        }

        // Return or do whatever you want with these counts
        return [
            'total_day'   => $totalDay,
            'total_night' => $totalNight,
        ];
    }


    public function getSummary(Request $request)
    {
        // dd($request->all());
        $yearMonth = $request->input('yearmonth', date('Y-m'));
        $holidays = $this->getHolidays();
        [$startDate, $endDate] = $this->getDateRange($yearMonth);
        $psps = $this->getAccessiblePsps();

        $tableRows = $this->getTableRows();
        $pspDailyData = [];
        $totalSummary = [];

        foreach ($psps as $psp) {
            $pspDailyData[$psp->id] = $this->buildPspDailyData($psp, $yearMonth, $endDate, $totalSummary);
        }

        // dd($pspDailyData);

        $summaryData = $this->buildSummaryData($psps, $pspDailyData, $yearMonth, $endDate, $tableRows, $holidays);

        if (!$this->user->hasRole('PSP')) {
            $grandTotalRows = $this->buildGrandTotalRows($tableRows, $totalSummary, $yearMonth, $endDate, $holidays);
            $summaryData = array_merge($grandTotalRows, $summaryData);
        }

        return $summaryData;
    }

    private function getHolidays(): array
    {
        return array_column(Holiday::select('date')->get()->toArray(), 'date');
    }

    private function getDateRange(string $yearMonth): array
    {
        $start = Carbon::parse($yearMonth . '-01')->startOfMonth();
        $end = ($yearMonth === date('Y-m')) ? Carbon::today() : $start->copy()->endOfMonth();
        return [$start, $end];
    }

    private function getAccessiblePsps()
    {
        if ($this->user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
            return Psp::all();
        } elseif ($this->user->hasRole('PSP')) {
            return [$this->user->psp];
        }
        return null;
    }

    private function getTableRows(): array
    {
        return [
            'Active',
            'Target',
            'Shortfall',
            'Joined',
            'Left',
            'Absent',
            'Absent%',
            'Present',
            'Half Day',
            'Missed Punch',
            'Wrong Shift',
            'Week Off',
        ];
    }

    private function buildPspDailyData($psp, string $yearMonth, $endDate, &$totalSummary): array
    {
        $data = [];

        for ($day = 1; $day <= $endDate->day; $day++) {
            $date = $yearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            $isToday = ($date == date('Y-m-d'));

            $summary = $this->calculateAttendanceSummary($psp, $date, $isToday);

            $data[$date] = $summary['daily'];
            $this->accumulateTotalSummary($totalSummary, $date, $summary);
        }

        return $data;
    }

    private function calculateAttendanceSummary($psp, $date, $isToday): array
    {
        $totalActive = $this->countActiveDayNight($psp->id, $date);
        $totalLeft = $this->countLeftDayNight($psp->id, $date);
        $totalJoined = $this->countJoinedDayNight($psp->id, $date);
        $attendance = $this->getAttendanceCount($psp, $date, $isToday);

        // Corrected Absent% calculation
        $dayTotal = $attendance['absent_day'] + $attendance['present_day'];
        $nightTotal = $attendance['absent_night'] + $attendance['present_night'];

        $absentDayPerc = $dayTotal > 0 ? round($attendance['absent_day'] / $dayTotal * 100, 0) : 0;
        $absentNightPerc = $nightTotal > 0 ? round($attendance['absent_night'] / $nightTotal * 100, 0) : 0;

        return [
            'daily' => [
                'Active' => $totalActive['total_day'] . ' | ' . $totalActive['total_night'],
                'Target' => $psp->day_worker_target . ' | ' . $psp->night_worker_target,
                'Shortfall' => $this->calculateShortfall($psp, $attendance),
                'Joined' => $totalJoined['total_day'] . ' | ' . $totalJoined['total_night'],
                'Left' => $totalLeft['total_day'] . ' | ' . $totalLeft['total_night'],
                'Absent' => $attendance['absent_day'] . ' | ' . $attendance['absent_night'],
                'Absent%' => $absentDayPerc . '% | ' . $absentNightPerc . '%',
                'Present' => $attendance['present_day'] . ' | ' . $attendance['present_night'],
                'Half Day' => $attendance['half_day_day'] . ' | ' . $attendance['half_day_night'],
                'Missed Punch' => $attendance['missed_punch_day'] . ' | ' . $attendance['missed_punch_night'],
                'Wrong Shift' => $attendance['wrong_shift_day'] . ' | ' . $attendance['wrong_shift_night'],
                'Week Off' => $attendance['week_off_day'] . ' | ' . $attendance['week_off_night'],
            ],
            'raw' => [
                'absent_day' => $attendance['absent_day'],
                'present_day' => $attendance['present_day'],
                'absent_night' => $attendance['absent_night'],
                'present_night' => $attendance['present_night'],
            ]
        ];
    }

    private function accumulateTotalSummary(&$summary, $date, $metrics)
    {
        if (!isset($summary['AbsentRaw'][$date])) {
            $summary['AbsentRaw'][$date] = [
                'absent_day' => 0,
                'present_day' => 0,
                'absent_night' => 0,
                'present_night' => 0,
            ];
        }

        $summary['AbsentRaw'][$date]['absent_day'] += $metrics['raw']['absent_day'];
        $summary['AbsentRaw'][$date]['present_day'] += $metrics['raw']['present_day'];
        $summary['AbsentRaw'][$date]['absent_night'] += $metrics['raw']['absent_night'];
        $summary['AbsentRaw'][$date]['present_night'] += $metrics['raw']['present_night'];

        // Also store other metrics (except Absent% and Present% which are derived)
        foreach ($metrics['daily'] as $metric => $value) {
            if (!in_array($metric, ['Absent%'])) {
                [$day, $night] = array_map('trim', explode('|', $value));
                if (!isset($summary[$metric][$date])) {
                    $summary[$metric][$date] = ['day' => 0, 'night' => 0];
                }

                $summary[$metric][$date]['day'] += (float) $day;
                $summary[$metric][$date]['night'] += (float) $night;
            }
        }
    }

    private function calculateShortfall($psp, $attendance): string
    {
        $shortFallDay = $psp->day_worker_target - ($attendance['present_day'] + ($attendance['half_day_day'] / 2));
        $shortFallNight = $psp->night_worker_target - ($attendance['present_night'] + ($attendance['half_day_night'] / 2));
        return $shortFallDay . ' | ' . $shortFallNight;
    }

    private function getAttendanceCount($psp, $date, $isToday): array
    {
        $attendanceAggregationQuery = ProcessedAttendance::whereDate('date', $date)
            ->whereHas('worker', function ($query) use ($date, $psp) {
                $query
                    ->where('psp_id', $psp->id)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('date_of_joining')
                            ->orWhere('date_of_joining', '<=', $date);
                    })
                    ->where(function ($q) use ($date) {
                        $q->whereNull('date_of_leaving')
                            ->orWhere('date_of_leaving', '>=', $date);
                    });
            });
        // For each metric we add separate CASE expressions for day and night.
        $attendanceAggregationQuery
            ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status IN ('A', 'S/W') THEN 1 ELSE 0 END) as absent_day")
            ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status IN ('A', 'S/W') THEN 1 ELSE 0 END) as absent_night");

        // Present: if today, consider both 'P' and 'M/P', otherwise only 'P'.
        if ($isToday) {
            $attendanceAggregationQuery
                ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status IN ('P', 'M/P') THEN 1 ELSE 0 END) as present_day")
                ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status IN ('P', 'M/P') THEN 1 ELSE 0 END) as present_night");
        } else {
            $attendanceAggregationQuery
                ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status = 'P' THEN 1 ELSE 0 END) as present_day")
                ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status = 'P' THEN 1 ELSE 0 END) as present_night");
        }
        // Half Day
        $attendanceAggregationQuery
            ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status = 'H/D' THEN 1 ELSE 0 END) as half_day_day")
            ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status = 'H/D' THEN 1 ELSE 0 END) as half_day_night");

        // Missed Punch: if today, set to 0.
        if ($isToday) {
            $attendanceAggregationQuery
                ->selectRaw("0 as missed_punch_day")
                ->selectRaw("0 as missed_punch_night");
        } else {
            $attendanceAggregationQuery
                ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status = 'M/P' THEN 1 ELSE 0 END) as missed_punch_day")
                ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status = 'M/P' THEN 1 ELSE 0 END) as missed_punch_night");
        }
        // Wrong Shift
        $attendanceAggregationQuery
            ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status = 'WRONG SHIFT' THEN 1 ELSE 0 END) as wrong_shift_day")
            ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status = 'WRONG SHIFT' THEN 1 ELSE 0 END) as wrong_shift_night");

        // Week Off (W/O or HL)
        $attendanceAggregationQuery
            ->selectRaw("SUM(CASE WHEN shift_type = 'day' AND status IN ('W/O', 'HL') THEN 1 ELSE 0 END) as week_off_day")
            ->selectRaw("SUM(CASE WHEN shift_type = 'night' AND status IN ('W/O', 'HL') THEN 1 ELSE 0 END) as week_off_night");

        $attendanceAggregation = $attendanceAggregationQuery->first();

        return (array) $attendanceAggregation->toArray();
    }

    private function buildSummaryData($psps, $pspDailyData, $yearMonth, $endDate, $tableRows, $holidays)
    {
        $summaryData = [];

        foreach ($psps as $psp) {
            foreach ($tableRows as $status) {
                $row = [
                    'psp' => $status === 'Active' ? $psp->name : '',
                    'status' => $status,
                ];

                $statusTotalDay = [];
                $statusTotalNight = [];
                $workingDays = 0;

                for ($day = 1; $day <= $endDate->day; $day++) {
                    $date = $yearMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $value = $pspDailyData[$psp->id][$date][$status] ?? '0 | 0';

                    $row[$date] = $value;

                    $dateParsed = Carbon::parse($date);
                    if (!in_array($dateParsed->format('Y-m-d'), $holidays) && !$dateParsed->isSunday()) {
                        [$dayCount, $nightCount] = array_map('trim', explode('|', $value));
                        $statusTotalDay[] = (float) $dayCount;
                        $statusTotalNight[] = (float) $nightCount;
                        $workingDays++;
                    }
                }

                $row['max'] = (!empty($statusTotalDay) ? round(max($statusTotalDay), 0) : 0)
                    . ' | '
                    . (!empty($statusTotalNight) ? round(max($statusTotalNight), 0) : 0);

                $row['avg'] = ($workingDays > 0 ? round(array_sum($statusTotalDay) / $workingDays, 0) : 0)
                    . ' | '
                    . ($workingDays > 0 ? round(array_sum($statusTotalNight) / $workingDays, 0) : 0);

                $summaryData[] = $row;
            }
        }

        return $summaryData;
    }

    private function buildGrandTotalRows($statuses, $summary, $yearMonth, $endDate, $holidays)
    {
        $rows = [];

        foreach ($statuses as $status) {
            $row = [
                'psp' => ($status === 'Active' ? 'All PSP' : ''),
                'status' => $status,
            ];

            $dayTotals = [];
            $nightTotals = [];
            $workingDays = 0;

            for ($d = 1; $d <= $endDate->day; $d++) {
                $date = $yearMonth . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $parsed = Carbon::parse($date);

                if ($status === 'Absent%') {
                    $r = $summary['AbsentRaw'][$date] ?? null;

                    $denomDay = $r['absent_day'] + $r['present_day'];
                    $denomNight = $r['absent_night'] + $r['present_night'];

                    $percDay = ($denomDay > 0) ? round($r['absent_day'] / $denomDay * 100, 0) : 0;
                    $percNight = ($denomNight > 0) ? round($r['absent_night'] / $denomNight * 100, 0) : 0;

                    $row[$date] = "$percDay% | $percNight%";

                    if (!in_array($date, $holidays) && !$parsed->isSunday()) {
                        $dayTotals[] = $percDay;
                        $nightTotals[] = $percNight;
                        $workingDays++;
                    }
                } elseif (isset($summary[$status][$date])) {
                    $day = $summary[$status][$date]['day'] ?? 0;
                    $night = $summary[$status][$date]['night'] ?? 0;

                    $row[$date] = $day . ' | ' . $night;

                    if (!in_array($date, $holidays) && !$parsed->isSunday()) {
                        $dayTotals[] = $day;
                        $nightTotals[] = $night;
                        $workingDays++;
                    }
                } else {
                    $row[$date] = '0 | 0';
                }
            }

            $row['max'] = (!empty($dayTotals) ? round(max($dayTotals), 0) : 0)
                . ' | '
                . (!empty($nightTotals) ? round(max($nightTotals), 0) : 0);

            $row['avg'] = ($workingDays > 0 ? round(array_sum($dayTotals) / $workingDays, 0) : 0)
                . ' | '
                . ($workingDays > 0 ? round(array_sum($nightTotals) / $workingDays, 0) : 0);

            $rows[] = $row;
        }

        return $rows;
    }
}
