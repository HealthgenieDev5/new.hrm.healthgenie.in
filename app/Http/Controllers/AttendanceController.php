<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Level;
use App\Models\ProductionProduct;
use App\Models\Psp;
use App\Models\Shift;
use App\Models\Worker;
use App\Models\Workertype;
use App\Models\WorkingArea;
use App\Services\AttendanceApi\AttendanceApiHeadOfficeMachine;
use App\Services\AttendanceApi\AttendanceApiMachineOne;
use App\Services\AttendanceApi\AttendanceApiMachineThree;
use App\Services\AttendanceApi\AttendanceApiMachineTwo;
use App\Services\MissedPunchDatatableService;
use App\Services\WorkerAttendanceService;
use App\Services\WrongShiftDatatableService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /** @var \App\Models\User|null */
    private $user;

    public function __construct(private WorkerAttendanceService $WorkerAttendanceService)
    {
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        if ($this->user->hasRole('User')) {
            return view('user-dashboard', compact('pageTitle'));
        }
        // $request->input('filter_yearmonth') = $request->input('yearmonth');

        $currentDate = Carbon::now()->toDateString();
        $yearMonth = $request->input('yearmonth') ?? Carbon::now()->year . '-' . Carbon::now()->month;
        $datesInMonth = $this->WorkerAttendanceService->getTimeAttendanceDatesInMonth($request);
        $columns = $this->WorkerAttendanceService->generateTimeAttendanceColumns($request);
        // dd($columns);
        $pageTitle = 'Time Attendance ' . date('F Y', strtotime($yearMonth));
        $psps = Psp::all();
        $shifts = Shift::all();
        $workers = $this->GetFilterdWorkers($request);
        $user = $this->user;

        return view('attendance.index', compact('pageTitle', 'datesInMonth', 'currentDate', 'columns', 'user', 'psps', 'shifts', 'workers', 'yearMonth'));
    }

    public function getAttendance(Request $request)
    {
        if ($request->ajax()) {
            $datesInMonth = $this->WorkerAttendanceService->getTimeAttendanceDatesInMonth($request);
            $monthlyAttendance = $this->WorkerAttendanceService->getMonthlyAttendance($request);
            // dd($monthlyAttendance);
            // Define the columns that contain raw HTML
            $rawColumns = array_merge(
                ['code', 'name'],
                $datesInMonth->toArray()
            );

            return datatables()
                ->of($monthlyAttendance)
                ->rawColumns($rawColumns)
                ->make(true);

            return $response;
        }
    }

    public function GetFilterdWorkers(Request $request)
    {
        $workersQuery = Worker::query();
        if ($request->psp && $request->psp != 'all') {
            $workersQuery->where('psp_id', $request->psp);
        }
        $workersQuery->orderBy('code', 'asc');

        return $request->ajax() ? response()->json($workersQuery->select('id', 'name', 'code')->get()) : $workersQuery->get();
    }

    public function GetRawData(Request $request)
    {
        $worker = Worker::where('employment_status', 'Active')->findOrFail($request->worker_id);
        $rawAttendance = [];
        if (! empty($worker?->code)) {
            $rawAttendance = Attendance::where('empcode', $worker->code)->where('date', $request->date)->where('shift_type', $request->shift_type)->first();
        }
        $intime = isset($rawAttendance->intime) && ! empty($rawAttendance->intime) ? date('H:i', strtotime($rawAttendance->intime)) : null;
        $outtime = isset($rawAttendance->outtime) && ! empty($rawAttendance->outtime) ? date('H:i', strtotime($rawAttendance->outtime)) : null;

        return response()->json(['intime' => $intime, 'outtime' => $outtime]);
    }

    public function missedPunch(Request $request)
    {
        if ($this->user->hasRole('User')) {
            return abort('403', 'You are not authorised to view this page');
        }

        // Initialize variables
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
        $currentDate = Carbon::now()->toDateString();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $datesInMonth->push(Carbon::create($year, $month, $i)->toDateString());
        }

        $MissedPunchDatatableService = new MissedPunchDatatableService;
        $columns = $MissedPunchDatatableService->generateColumns($this->user, $datesInMonth);
        // dd($columns);

        $pageTitle = 'Missed Punch Report ' . date('F Y', strtotime($year . '-' . $month));
        $psps = Psp::all();
        $shifts = Shift::all();
        $workers = $this->GetFilterdWorkers($request);
        $user = $this->user;
        // dd($workers);

        return view('attendance.missed-punch.index', compact('pageTitle', 'datesInMonth', 'currentDate', 'user', 'psps', 'shifts', 'workers', 'columns', 'yearMonth'));
    }

    public function getMissedPunchDatatable(Request $request)
    {
        $MissedPunchDatatableService = new MissedPunchDatatableService;

        return $MissedPunchDatatableService->getDatatable($request);
    }

    public function wrongShift(Request $request)
    {
        if ($this->user->hasRole('User')) {
            return abort('403', 'You are not authorised to view this page');
        }

        // Initialize variables
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
        $currentDate = Carbon::now()->toDateString();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $datesInMonth->push(Carbon::create($year, $month, $i)->toDateString());
        }

        $WrongShiftDatatableService = new WrongShiftDatatableService;
        $columns = $WrongShiftDatatableService->generateColumns($this->user, $datesInMonth);
        // dd($columns);

        $pageTitle = 'Wrong Shift Report ' . date('F Y', strtotime($year . '-' . $month));
        $psps = Psp::all();
        $shifts = Shift::all();
        $workers = $this->GetFilterdWorkers($request);
        $user = $this->user;
        // dd($workers);

        return view('attendance.wrong-shift.index', compact('pageTitle', 'datesInMonth', 'currentDate', 'user', 'psps', 'shifts', 'workers', 'columns', 'yearMonth'));
    }

    public function getWrongShiftDatatable(Request $request)
    {
        $WrongShiftDatatableService = new WrongShiftDatatableService;

        return $WrongShiftDatatableService->getDatatable($request);
    }

    public function summary(Request $request)
    {
        $queryParams = $request->all();
        $query = http_build_query($queryParams);
        // Step 1: Clean the array
        $cleanedParams = $this->cleanUpRequest($queryParams);
        $cleanedQuery = http_build_query($cleanedParams);
        // dd([$query, $cleanedQuery]);

        // Step 2: Compare arrays, not strings
        if ($queryParams !== $cleanedParams) {
            return redirect()->to(route('attendance.summary') . '?' . $cleanedQuery);
        }

        if ($this->user->hasRole('User')) {
            return view('user-dashboard', compact('pageTitle'));
        }
        // Initialize variables
        $date_range = $request->input('date_range') ?? date('Y-m-01') . ' to ' . date('Y-m-d');
        $date_range_array = explode('to', $date_range);
        if (! isset($date_range_array[1])) {
            $date_range_array[1] = $date_range_array[0];
        }
        // $yearMonth = $request->input('yearmonth') ?? Carbon::now()->year . '-' . Carbon::now()->month;
        $pageTitle = 'Monthly Attendance Summary ' . date('d M Y', strtotime($date_range_array[0])) . ' to ' . date('d M Y', strtotime($date_range_array[1]));

        // $psps = Psp::all();
        // $shifts = Shift::all();
        $workers = $this->GetFilterdWorkers($request);
        $workerSummaryColumns = $this->WorkerAttendanceService->getMonthlyAttendanceSummaryColumns();
        $user = $this->user;

        // $workerTypes = Workertype::all();
        // $workingareas = WorkingArea::all();
        // $designations = Designation::all();
        // $productionProducts = ProductionProduct::all();
        // $levels = Level::all();

        $filters = Cache::remember('attendance_filter_data', now()->addHours(12), function () {
            return [
                'psps' => Psp::select('id', 'name')->get(),
                'shifts' => Shift::select('id', 'name')->get(),
                'workerTypes' => Workertype::select('id', 'name')->get(),
                'workingareas' => WorkingArea::select('id', 'name')->get(),
                'designations' => Designation::select('id', 'name')->get(),
                'productionProducts' => ProductionProduct::select('id', 'name')->get(),
                'levels' => Level::select('id', 'name')->get(),
                'divisions' => Division::with('departments.subdepartments.lines.processes')->get(),
            ];
        });

        // $divisions = Division::with('departments.subdepartments.lines.processes')->get();

        // return view('attendance.summary', compact('pageTitle', 'user', 'psps', 'shifts', 'workers', 'workerSummaryColumns', 'date_range', 'tableTitle', 'workerTypes', 'workingareas', 'designations', 'productionProducts', 'levels', 'divisions'));

        return view('attendance.summary', array_merge(
            compact('pageTitle', 'user', 'workers', 'workerSummaryColumns', 'date_range'),
            $filters
        ));
    }

    private function cleanUpRequest($queryParams)
    {
        $cleaned = [];

        foreach ($queryParams as $key => $value) {
            if ($key == 'employment_status' && $value === 'all') {
                $cleaned[$key] = $value;
            } else {
                if (is_array($value)) {
                    $filtered = array_filter($value, fn($v) => $v !== '' && $v !== 'all');

                    if (! empty($filtered)) {
                        $cleaned[$key] = array_values($filtered); // reset array keys
                    }
                } else {
                    if ($value !== '' && $value !== 'all' && $value !== null) {
                        $cleaned[$key] = $value;
                    }
                }
            }
        }

        return $cleaned;
    }

    public function getAttendanceSummary(Request $request)
    {
        $summary = $this->WorkerAttendanceService->getMonthlyAttendanceSummary($request);
        // dd($summary);
        $rawColumns = ['absent_percentage'];

        return DataTables::of($summary)
            ->rawColumns($rawColumns)
            ->make(true);
    }

    public function ContinuousAbsent(Request $request)
    {
        if ($this->user->hasRole('User')) {
            return view('user-dashboard', compact('pageTitle'));
        }
        // Initialize variables
        $yearMonth = $request->input('yearmonth') ?? Carbon::now()->year . '-' . Carbon::now()->month;
        $pageTitle = 'Continuous Absent ' . date('F Y', strtotime($yearMonth . ' -1 days'));
        $pageTitle .= ' - ' . date('F Y', strtotime($yearMonth));
        $psps = Psp::all();
        $shifts = Shift::all();
        $workers = $this->GetFilterdWorkers($request);
        $continuousAbsentColumns = $this->WorkerAttendanceService->getContinuousAbsentColumns();
        $user = $this->user;

        // $this->WorkerAttendanceService->getContinuousAbsent($request)->dd();

        return view('attendance.continuous-absent', compact('pageTitle', 'user', 'psps', 'shifts', 'workers', 'continuousAbsentColumns', 'yearMonth'));
    }

    public function getContinuousAbsent(Request $request)
    {
        $continuousAbsent = $this->WorkerAttendanceService->getContinuousAbsent($request);
        // dd($summary);
        $rawColumns = ['absent_percentage'];

        return DataTables::of($continuousAbsent)
            ->rawColumns($rawColumns)
            ->make(true);
    }

    public function recalculate(Request $request)
    {
        if ($this->user->hasRole('User')) {
            return view('user-dashboard', compact('pageTitle'));
        }
        $yearMonth = $request->input('yearmonth') ?? Carbon::now()->year . '-' . Carbon::now()->month;
        $pageTitle = 'Recalculate Attendance';
        $psps = Psp::all();
        $workers = $this->GetFilterdWorkers($request);
        $user = $this->user;

        return view('attendance.recalculate', compact('pageTitle', 'user', 'psps', 'workers', 'yearMonth'));
    }

    public function refreshAttendance(Request $request)
    {
        // dd($request->all());
        $workerCode = $request->input('worker');
        $yearMonth = $request->input('yearmonth') ?? date('Y-m');

        $workerExists = Worker::where('code', $workerCode)->exists();

        if (! $workerExists) {
            return back()->with('error', 'Worker not found.');
        }

        $fromDate = date('01/m/Y', strtotime($yearMonth . '-01'));
        if ($yearMonth == date('Y-m')) {
            $toDate = date('d/m/Y');
        } else {
            $toDate = date('t/m/Y', strtotime($yearMonth . '-01'));
        }

        // Artisan::call('attendance:fetch', [
        //     '--worker' => $workerCode,
        //     '--month' => $yearMonth
        // ]);

        // $attendanceApiMachineOne = new AttendanceApiMachineOne;
        // $attendanceApiMachineTwo = new AttendanceApiMachineTwo;
        // $attendanceApiMachineThree = new AttendanceApiMachineThree;
        // $AttendanceApiHeadOfficeMachine = new AttendanceApiHeadOfficeMachine;
        $attendanceApiMachineOne = new AttendanceApiMachineOne();
        $attendanceApiMachineTwo = new AttendanceApiMachineTwo();
        $attendanceApiMachineThree = new AttendanceApiMachineThree();
        $AttendanceApiHeadOfficeMachine = new AttendanceApiHeadOfficeMachine();
        $attendanceApiMachineOne->storeAttendance($workerCode, $fromDate, $toDate);
        $attendanceApiMachineTwo->storeAttendance($workerCode, $fromDate, $toDate);
        $attendanceApiMachineThree->storeAttendance($workerCode, $fromDate, $toDate);
        $AttendanceApiHeadOfficeMachine->storeAttendance($workerCode, $fromDate, $toDate);

        // Artisan::call('attendance:process', [
        //     '--workers' => $workerCode,
        //     '--month' => $yearMonth
        // ]);

        Artisan::call('attendance:process-worker', [
            'code' => $workerCode,
            '--month' => $yearMonth,
        ]);




        // dd([
        //     'arrResponse' => $arrResponse,
        //     'output' => Artisan::output(),
        //     'worker_code' => $workerCode,
        //     'year_month' => $yearMonth,
        // ]);
        return redirect()
            ->route('attendance.recalculate', [
                'worker' => $workerCode,
                'yearmonth' => $yearMonth,
            ])
            ->with('success', 'Attendance re processed successfully!');
    }
}
