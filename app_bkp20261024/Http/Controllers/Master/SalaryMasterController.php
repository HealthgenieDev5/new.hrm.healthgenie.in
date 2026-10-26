<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\SalaryMaster;
use App\Http\Requests\StoreSalaryMasterRequest;
use App\Http\Requests\UpdateSalaryMasterRequest;
use App\Models\Psp;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalaryMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $psps = Psp::all();

        $workersQuery = Worker::select('id', 'code', 'name');
        $psp_request = $request->psp;
        $workers_request = $request->workers;

        if (!empty($psp_request) && $psp_request != 'all') {
            $workersQuery->where('psp_id', $psp_request);
        }

        $workers = $workersQuery->get();

        $salaryMastersQuery = Worker::with(['salaryStructure', 'workerType']);


        // $today = date('Y-m-d');
        // $firstOfMonth = Carbon::parse($today)->startOfMonth();

        // $salaryMastersQuery->where(function ($query) use ($firstOfMonth) {
        //     $query->whereNull('date_of_leaving')
        //         ->orWhere('date_of_leaving', '>=', $firstOfMonth);
        // });

        // $lastOfMonth = Carbon::parse($today)->lastOfMonth();

        // $salaryMastersQuery->where(function ($query) use ($lastOfMonth) {
        //     $query->whereNull('date_of_joining')
        //         ->orWhere('date_of_joining', '<=', $lastOfMonth);
        // });

        if ($request->employment_status && $request->employment_status === 'non-active') {
            $salaryMastersQuery->where('employment_status', '!=', 'active');
        } else {
            $salaryMastersQuery->where('employment_status', 'active');
        }

        if (!empty($psp_request) && $psp_request != 'all') {
            $salaryMastersQuery->where('psp_id', $psp_request);
        }
        if (!empty($workers_request) && !in_array('all', $workers_request)) {
            $salaryMastersQuery->whereIn('id', $workers_request);
        }


        $salaryMasters = $salaryMastersQuery->get();
        // dd($salaryMasters);

        $pageTitle = 'Salary Master';

        // $salaryMastersQuery = SalaryMaster::query()
        //     ->when(!empty($request->psp) && $request->psp !== 'all', function ($query) use ($request) {
        //         return $query->whereHas('worker', function ($q) use ($request) {
        //             $q->where('psp_id', $request->psp);
        //         });
        //     })
        //     ->when(!empty($request->workers) && is_array($request->workers) && !in_array('all', $request->workers), function ($query) use ($request) {
        //         return $query->whereHas('worker', function ($q) use ($request) {
        //             $q->whereIn('id', $request->workers);
        //         });
        //     });

        // $salaryMasters = $salaryMastersQuery->get()->all();

        return view('master.salary_master.index', compact('salaryMasters', 'pageTitle', 'psps', 'workers'));
        // return view('master.salary_master.index', compact('pageTitle', 'psps', 'workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Salary Master - Create New'; // Page title for the create view
        $workers = Worker::select('id', 'code', 'name')->get();
        return view('master.salary_master.create', compact('pageTitle', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalaryMasterRequest $request)
    {
        // dd($request->user->hasPermission('Create Salary Master'));
        try {
            SalaryMaster::create($request->validated());
        } catch (\Exception $e) {
            // Log::error('Error fetching attendance: ' . $e->getMessage());
            //throw $th;
            dd($e->getMessage());
        }

        return redirect()->route('master.salary-master.index')->with('success', 'Salary structure created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryMaster $salaryMaster)
    {
        // return view('salary_master.show', compact('salaryMaster'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryMaster $salaryMaster)
    {
        $pageTitle = 'Salary Master - Edit'; // Page title for the create view
        $workers = Worker::select('id', 'code', 'name')->get();
        return view('master.salary_master.edit', compact('salaryMaster', 'pageTitle', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalaryMasterRequest $request, SalaryMaster $salaryMaster)
    {


        $salaryMaster->update($request->validated());

        return redirect()->route('master.salary-master.index')->with('success', 'Salary structure updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryMaster $salaryMaster)
    {
        $salaryMaster->delete();
        return redirect()->route('master.salary-master.index')->with('success', 'Salary structure deleted successfully.');
    }
}
