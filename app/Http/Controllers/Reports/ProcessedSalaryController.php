<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\ProcessedSalary;
use App\Http\Requests\StoreProcessedSalaryRequest;
use App\Http\Requests\UpdateProcessedSalaryRequest;
use App\Interfaces\SalaryProcessingServiceInterface;
use App\Models\Psp;
use App\Models\Worker;
// use App\Services\Contracts\SalaryProcessingServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ProcessedSalaryController extends Controller
{
    // protected $salaryProcessingServiceInterface;
    private $user;

    public function __construct(protected SalaryProcessingServiceInterface $salaryProcessingServiceInterface)
    {
        // $this->salaryProcessingServiceInterface = $salaryProcessingServiceInterface;
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        #Initialize variables
        $yearMonth = $request->input('yearmonth');
        if ($yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
        } else {
            $lastMonth = Carbon::now()->subMonth();
            $year = $lastMonth->year;
            $month = $lastMonth->month;
            $yearMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $pageTitle = "Monthly Salary " . date('F Y', strtotime($year . '-' . $month));
        $psps = Psp::all();
        $workers = $this->salaryProcessingServiceInterface->getFilteredWorkers($request);
        $workerIds = $workers?->pluck('id')->toArray();
        // dd($workerIds);
        $processedSalaries = $this->salaryProcessingServiceInterface->getProcessedSalaries($year, $month, $workerIds);
        $user = $this->user;
        return view('reports.salary-report.index', compact('pageTitle', 'user', 'psps', 'workers', 'yearMonth', 'processedSalaries', 'yearMonth'));
    }

    public function incentives(Request $request)
    {
        #Initialize variables
        $yearMonth = $request->input('yearmonth');
        if ($yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
        } else {
            $lastMonth = Carbon::now()->subMonth();
            $year = $lastMonth->year;
            $month = $lastMonth->month;
            $yearMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $pageTitle = "Monthly Incentive " . date('F Y', strtotime($year . '-' . $month));
        $psps = Psp::all();
        // dd($psps);
        $workers = $this->salaryProcessingServiceInterface->getFilteredWorkers($request);
        $workerIds = $workers?->pluck('id')->toArray();
        // dd($workerIds);
        $processedIncentives = $this->salaryProcessingServiceInterface->getProcessedIncentives($year, $month, $workerIds);
        $user = $this->user;
        return view('reports.salary-report.incentives', compact('pageTitle', 'user', 'psps', 'workers', 'yearMonth', 'processedIncentives', 'yearMonth'));
    }

    public function getFilteredWorkers(Request $request)
    {
        // dd($request->all());
        return $this->salaryProcessingServiceInterface->getFilteredWorkers($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProcessedSalaryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProcessedSalary $processedSalary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcessedSalary $processedSalary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProcessedSalaryRequest $request, ProcessedSalary $processedSalary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcessedSalary $processedSalary)
    {
        //
    }
}
