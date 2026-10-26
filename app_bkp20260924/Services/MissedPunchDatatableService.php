<?php

namespace App\Services;

use App\Models\ProcessedAttendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissedPunchDatatableService
{
    private $user;

    public function __construct()
    {
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }

    public function getDatatable(Request $request)
    {
        if ($request->ajax()) {
            return $this->generateDatatable($request);
        }
    }

    private function generateDatatable($request)
    {

        #Begin:: get from request, if not then current month
        $yearMonth = $request->filter_yearmonth ?? date('Y-m');
        if ($yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
        }

        $workersQuery = Worker::query();

        if ($request->filter_psp && $request->filter_psp != 'all') {
            $workersQuery->where('psp_id', $request->filter_psp);
        }

        if ($request->filter_workers && !in_array('all', $request->filter_workers)) {
            $workersQuery->whereIn('id', $request->filter_workers);
        }

        if ($request->filter_shift && $request->filter_shift != 'all') {
            $workersQuery->where('shift_id', $request->filter_shift);
        }

        if ($this->user->hasRole('PSP')) {
            $workersQuery->where('psp_id', $this->user->psp->id);
        }

        $firstOfMonth = Carbon::parse($year . '-' . $month . '-01')->startOfMonth();
        $workersQuery->where(function ($query) use ($firstOfMonth) {
            $query->whereNull('date_of_leaving')
                ->orWhere('date_of_leaving', '>=', $firstOfMonth);
        });

        $ajaxWorkers = $workersQuery->get();

        $workerEmpcodes = $ajaxWorkers?->pluck('id');

        #End:: get from request, if not then current month

        $attendanceRecords = ProcessedAttendance::whereIn('worker_id', $workerEmpcodes)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'ASC')
            ->get()
            ->groupBy('worker_id');
        $data = $ajaxWorkers->flatMap(function ($worker) use ($attendanceRecords, $yearMonth) {
            return $this->transformWorkerData($worker, $attendanceRecords, $yearMonth);
        });

        //Sorting logic
        if ($request->has('order')) {
            $orderColumn = $request->input('order.0.column'); // Get column index
            $orderDir = $request->input('order.0.dir') === 'asc' ? 'ASC' : 'DESC';

            $columns = $this->generateColumns();
            $columns = array_column($columns, 'data');

            if (isset($columns[$orderColumn])) {
                $data = $data->sortBy($columns[$orderColumn], SORT_REGULAR, $orderDir === 'DESC');
            }
        }

        #Define the columns that contain raw HTML
        $rawColumns = ['code', 'name', 'attendance_link'];

        return datatables()
            ->of($data)
            ->rawColumns($rawColumns)
            ->make(true);
    }

    # Additional Helper Methods
    public function generateColumns()
    {
        $columns = [];

        if (!$this->user->hasRole('PSP')) {
            $columns[] = ['data' => 'psp', 'name' => 'PSP<br><br>', 'searchable' => true, 'orderable' => true];
        }

        $columns[] = ['data' => 'code', 'name' => 'Worker Code<br><br>', 'searchable' => true, 'orderable' => true];
        $columns[] = ['data' => 'name', 'name' => 'Worker Name<br><br>', 'searchable' => true, 'orderable' => true];

        if (!$this->user->hasRole('PSP')) {
            $columns[] = ['data' => 'column_name', 'name' => 'Column<br><br>', 'searchable' => true, 'orderable' => true];
        }

        $columns[] = ['data' => 'missed_punch', 'name' => 'Missed Punch<br><br>', 'searchable' => true, 'orderable' => true];

        $columns[] = ['data' => 'days_in_month', 'name' => 'Month Days<br><br>', 'searchable' => true, 'orderable' => true];

        $columns[] = ['data' => 'attendance_link', 'name' => 'Attendance link<br><br>', 'searchable' => false];

        $columns[] = ['data' => 'searchable_name', 'name' => 'searchable_name', 'visible' => false, 'searchable' => true, 'orderable' => true];

        return $columns;
    }

    private function transformWorkerData($worker, $attendanceRecords, $yearMonth)
    {
        $attendanceRecordsOfThisWorker = $attendanceRecords[$worker->id] ?? false;
        $daysInMonth = Carbon::parse($yearMonth . '-01')->daysInMonth;

        $missedPunchCount = $this->getMissedPunchCount($attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->toArray() : []);

        if ($missedPunchCount == 0) {
            return []; // Return an empty array to exclude from datatable
        }

        $attendanceUrl = route('attendance.index', [
            'psp' => 'all',
            'workers' => [$worker->id],
            'shift' => 'all',
            'yearmonth' => $yearMonth,
        ]);

        $mainRow = [
            'code' => $worker->code,
            'name' => $worker->name,
            'missed_punch' => $missedPunchCount,
            'attendance_link' => "<a href='{$attendanceUrl}' target='_blank' style='color: blue; text-decoration: underline;'>View Attn</a>",
            'days_in_month' => $daysInMonth,
            'searchable_name' => $worker->name . "|" . $worker->code
        ];

        if ($this->user->hasRole('PSP')) {
            return [$mainRow];
        }

        # If not a psp
        $mainRow['column_name'] = "Status";
        $mainRow['psp'] = $worker->psp->name;

        return [$mainRow];
    }

    private function getMissedPunchCount($attendanceRecords)
    {
        return $attendanceRecords
            ? count(array_filter($attendanceRecords, function ($attendance) {
                $attendanceDate = date('Y-m-d', strtotime($attendance['date']));
                $todayDate = date('Y-m-d');
                return in_array($attendance['status'], ['M/P'])
                    && $attendanceDate != $todayDate;
            }))
            : 0;
    }
}
