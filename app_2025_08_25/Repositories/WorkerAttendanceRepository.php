<?php

namespace App\Repositories;

use App\Interfaces\WorkerAttendanceRepositoryInterface;
use App\Models\ProcessedAttendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class WorkerAttendanceRepository implements WorkerAttendanceRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getMonthlyAttendanceSummaryWorkers(Request $request): Collection
    {
        $date_range = $request->input('filter_date_range') ?? date('Y-m-01') . ' to ' . date('Y-m-d');
        $date_range_array = explode("to", $date_range);
        if (!isset($date_range_array[1])) {
            $date_range_array[1] = $date_range_array[0];
        }
        $startDate = Carbon::parse($date_range_array[0])->startOfDay();
        $endDate = Carbon::parse($date_range_array[1])->startOfDay();

        $workersQuery = Worker::with([
            'workertype',
            'designation',
            'division',
            'department',
            'subdepartment',
            'workingarea',
            'productionproduct',
            'line',
            'process',
            'level',
            'psp',
            'shift',
        ]);

        $workerIds = (array) $request->filter_workers;
        if (!empty($workerIds) && !in_array('all', $workerIds)) {
            $workersQuery->whereIn('id', $workerIds);
        }

        if ($request->filled('filter_shift_type') && $request->filter_shift_type !== 'all') {
            $workersQuery->whereHas('shift', function ($query) use ($request) {
                $query->where('shift_type', $request->filter_shift_type);
            });
        }

        if ($request->filled('filter_employment_status')) {
            if ($request->filter_employment_status === 'active') {
                $workersQuery->where('employment_status', 'active');
            } elseif ($request->filter_employment_status === 'non-active') {
                $workersQuery->where('employment_status', '!=', 'active');
            }
        }

        $filters = [
            'filter_psp' => 'psp_id',
            'filter_designation' => 'designation_id',
            'filter_workertype' => 'workertype_id',
            'filter_workingarea' => 'working_area_id',
            'filter_production_product' => 'production_product_id',
            'filter_level' => 'level_id',
            'filter_division' => 'division_id',
            'filter_department' => 'department_id',
            'filter_subdepartment' => 'subdepartment_id',
            'filter_line' => 'line_id',
            'filter_process' => 'process_id',
            'filter_shift' => 'shift_id',
        ];

        foreach ($filters as $filter => $column) {
            if ($request->filled($filter) && $request->$filter !== 'all') {
                $workersQuery->where($column, $request->$filter);
            }
        }

        $workersQuery->where(function ($query) use ($startDate) {
            $query->whereNull('date_of_leaving')
                ->orWhere('date_of_leaving', '>=', $startDate);
        });

        $workersQuery->where(function ($query) use ($endDate) {
            $query->whereNull('date_of_joining')
                ->orWhere('date_of_joining', '<=', $endDate);
        });

        // dd(
        //     vsprintf(str_replace('?', "'%s'", $workersQuery->toSql()), $workersQuery->getBindings())
        // );
        $workers = $workersQuery->get();

        return $workers;
    }

    public function getMonthlyAttendanceSummaryProcessedAttendance(Carbon $startDate, Carbon $endDate): Collection
    {
        $attendanceQuery = ProcessedAttendance::whereBetween('date', [$startDate, $endDate])
            ->with('worker');
        $attendanceRecords = $attendanceQuery->get();
        $groupedByWorker = $attendanceRecords->groupBy('worker_id');
        return $groupedByWorker;
    }

    public function getTimeAttendanceWorkers(Request $request)
    {
        $yearMonth = $request->input('yearmonth') ?? Carbon::now()->year . '-' . Carbon::now()->month;
        $workersQuery = Worker::select('id', 'code', 'name', 'employment_status', 'psp_id', 'shift_id', 'date_of_joining');

        if ($request->psp && $request->psp != 'all') {
            $workersQuery->where('psp_id', $request->psp);
        }

        if ($request->workers && !in_array('all', $request->workers)) {
            $workersQuery->whereIn('id', $request->workers);
        }

        if ($request->shift && $request->shift != 'all') {
            $workersQuery->where('shift_id', $request->shift);
        }

        $firstOfMonth = Carbon::parse($yearMonth . '-01')->startOfMonth();

        $workersQuery->where(function ($query) use ($firstOfMonth) {
            $query->whereNull('date_of_leaving')
                ->orWhere('date_of_leaving', '>=', $firstOfMonth);
        });

        $lastOfMonth = Carbon::parse($yearMonth . '-01')->lastOfMonth();

        $workersQuery->where(function ($query) use ($lastOfMonth) {
            $query->whereNull('date_of_joining')
                ->orWhere('date_of_joining', '<=', $lastOfMonth);
        });

        $workersQuery->with([
            'processedAttendance' => function ($query) use ($firstOfMonth) {
                $query->whereYear('date', $firstOfMonth->year)
                    ->whereMonth('date', $firstOfMonth->month);
            }
        ]);

        $workersQuery->orderBy('code', 'asc');

        $ajaxWorkers = $workersQuery->get();
        return  $ajaxWorkers;
    }


    // public function getContinuousAbsentWorkerIds(Carbon $from, Carbon $to)
    // {
    //     $today = Carbon::today()->toDateString();

    //     return ProcessedAttendance::select('worker_id')
    //         ->whereBetween('date', [$from, $to])
    //         ->where(function ($query) use ($today) {
    //             $query->where('status', 'A')
    //                 ->orWhere('status', 'S/W')
    //                 ->orWhere(function ($q) use ($today) {
    //                     $q->where('status', 'M/P')
    //                         ->where('date', '!=', $today);
    //                 });
    //         })
    //         ->groupBy('worker_id')
    //         ->havingRaw('COUNT(*) = 3')
    //         ->pluck('worker_id');
    // }

    public function getContinuousAbsentWorkerIds(Carbon $from, Carbon $to, Request $request)
    {
        $today = Carbon::today()->toDateString();
        $absenceFilter = function ($query) use ($from, $to, $today) {
            $query->whereBetween('processed_attendance.date', [$from, $to])
                ->where(function ($status) use ($today) {
                    $status->where('processed_attendance.status', 'A')
                        ->orWhere('processed_attendance.status', 'S/W')
                        ->orWhere(function ($mispunch) use ($today) {
                            $mispunch->where('processed_attendance.status', 'M/P')->where('processed_attendance.date', '!=', $today);
                        });
                });
        };

        $query = Worker::query();
        $filter_psp = $request->filter_psp && $request->filter_psp != 'all' ? $request->filter_psp : false;
        $filter_workers = $request->filter_workers && !in_array('all', $request->filter_workers) ? $request->filter_workers : false;
        $filter_shift = $request->filter_shift && $request->filter_shift != 'all' ? $request->filter_shift : false;
        $query->when($filter_psp, fn($q) => $q->where('workers.psp_id', $filter_psp));
        $query->when($filter_workers, fn($q) => $q->whereIn('workers.id', $filter_workers));
        $query->when($filter_shift, fn($q) => $q->where('workers.shift_id', $filter_shift));

        $query->whereHas('processedAttendance', $absenceFilter);
        $query->withCount(['processedAttendance as absent_count' => $absenceFilter]);

        return $query->having('absent_count', '=', 3)->pluck('id');
    }

    public function getContinuousAbsentAttendanceSummary($startOfLM, $endOfLM, $startOfMTD, $endOfMTD, $absentWorkerIds)
    {
        $today = Carbon::today()->toDateString();
        return ProcessedAttendance::selectRaw("
        worker_id,

        -- LM Present
        SUM(
            CASE
                WHEN date BETWEEN ? AND ? AND status = 'P' THEN 1
                WHEN date BETWEEN ? AND ? AND status = 'H/D' THEN 0.5
                WHEN date BETWEEN ? AND ? AND status = 'M/P' AND date = ? THEN 1
                ELSE 0
            END
        ) AS lm_present,

        -- LM Absent
        SUM(
            CASE
                WHEN date BETWEEN ? AND ? AND status IN ('A', 'S/W') THEN 1
                WHEN date BETWEEN ? AND ? AND status = 'H/D' THEN 0.5
                WHEN date BETWEEN ? AND ? AND status = 'M/P' AND date != ? THEN 1
                ELSE 0
            END
        ) AS lm_absent,

        -- LM Leave
        SUM(
            CASE
                WHEN date BETWEEN ? AND ? AND status IN ('CL', 'EL') THEN 1
                ELSE 0
            END
        ) AS lm_leave,

        -- MTD Present
        SUM(
            CASE
                WHEN date BETWEEN ? AND ? AND status = 'P' THEN 1
                WHEN date BETWEEN ? AND ? AND status = 'H/D' THEN 0.5
                WHEN date BETWEEN ? AND ? AND status = 'M/P' AND date = ? THEN 1
                ELSE 0
            END
        ) AS mtd_present,

        -- MTD Absent
        SUM(
            CASE
                WHEN date BETWEEN ? AND ? AND status IN ('A', 'S/W') THEN 1
                WHEN date BETWEEN ? AND ? AND status = 'H/D' THEN 0.5
                WHEN date BETWEEN ? AND ? AND status = 'M/P' AND date != ? THEN 1
                ELSE 0
            END
        ) AS mtd_absent,

        -- MTD Leave
        SUM(
            CASE
                WHEN date BETWEEN ? AND ? AND status IN ('CL', 'EL') THEN 1
                ELSE 0
            END
        ) AS mtd_leave
    ", [
            // LM present
            $startOfLM,
            $endOfLM,
            $startOfLM,
            $endOfLM,
            $startOfLM,
            $endOfLM,
            $today,

            // LM absent
            $startOfLM,
            $endOfLM,
            $startOfLM,
            $endOfLM,
            $startOfLM,
            $endOfLM,
            $today,

            // LM leave
            $startOfLM,
            $endOfLM,

            // MTD present
            $startOfMTD,
            $endOfMTD,
            $startOfMTD,
            $endOfMTD,
            $startOfMTD,
            $endOfMTD,
            $today,

            // MTD absent
            $startOfMTD,
            $endOfMTD,
            $startOfMTD,
            $endOfMTD,
            $startOfMTD,
            $endOfMTD,
            $today,

            // MTD leave
            $startOfMTD,
            $endOfMTD,
        ])
            ->whereIn('worker_id', $absentWorkerIds)
            ->groupBy('worker_id')
            ->get()
            ->keyBy('worker_id');
    }

    public function getContinuousAbsentWorkerWithDetail($absentWorkerIds)
    {
        return Worker::with([
            'division',
            'department',
            'subdepartment',
            'line',
            'process',
            'level',
            'designation'
        ])
            ->whereIn('id', $absentWorkerIds)
            ->get();
    }
}
