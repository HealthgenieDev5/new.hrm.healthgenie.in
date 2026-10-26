<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Shift;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    /** @var \App\Models\User|null $user */
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function index(Request $request)
    {

        $psp = $this->user->psp;
        $myRoles = $this->user->roles->pluck('name');

        // dd($myRoles);

        $yearMonth = $request->input('yearmonth', date('Y-m'));
        $pageTitle = $psp ? $psp->name : $myRoles->implode(' - ');
        $pageTitle .= " Dashboard";
        $pageTitle .= date(' F Y', strtotime($yearMonth));
        $shifts = Shift::all();

        $holidays = Holiday::select('date')->get()->toArray();
        $holidays = array_column($holidays, 'date');
        // dd($holidays);

        $attendanceSummaryService = new AttendanceSummaryService();
        $columns = $attendanceSummaryService->generateColumns($yearMonth);

        return view('dashboard', compact('pageTitle', 'yearMonth', 'columns', 'shifts', 'holidays'));
    }

    public function getAttendanceSummary(Request $request)
    {
        $AttendanceSummaryService = new AttendanceSummaryService();
        $summaryData = $AttendanceSummaryService->getSummary($request);

        // dd($summaryData);
        return DataTables::of($summaryData)
            ->make(true);
    }
}
