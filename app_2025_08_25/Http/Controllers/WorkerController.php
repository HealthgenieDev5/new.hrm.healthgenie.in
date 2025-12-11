<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerLongFormRequest;

use App\Http\Requests\UpdateWorkerRequest;
use App\Models\Worker;
use App\Imports\WorkersImport;
use App\Interfaces\WorkerServiceInterface;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Level;
use App\Models\ProductionProduct;
use App\Models\Psp;
use App\Models\Shift;
use App\Models\Workertype;
use App\Models\WorkingArea;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class WorkerController extends Controller
{
    // protected WorkerServiceInterface $workerService;
    private $user;
    public function __construct(protected WorkerServiceInterface $workerService)
    {
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $psps = Psp::all();
        return view("workers.index", [
            "pageTitle" => "Workers",
            "columns" => $this->workerService->getColumns(),
            "psps" => $psps,
            "user" => $this->user
        ]);
    }

    public function getworkers(Request $request)
    {
        if ($request->ajax()) {
            return $this->workerService->getDataTables($request);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // if ($this->user->psp->id == '11') {
        //     abort('403', 'Not allowed');
        // }

        return $this->workerService->getCreateView();
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreWorkerRequest $request): RedirectResponse
    {
        $limitReached = $this->limitReahed($request);
        if ($limitReached) {
            abort("403", "You cannot add anymore workers to the selected shift");
        }

        $this->workerService->storeWorker($request);
        return redirect()->route('workers.index')->with('success', 'Worker added successfully.');
    }

    private function limitReahed($request): bool
    {
        $user = $this->user;
        $psp = $request?->psp_id ? Psp::findOrFail($request->psp_id) : $user?->psp;
        if (!$psp) {
            return true;
        }

        $shift = Shift::findOrFail($request->shift_id);
        $limit = $shift->shift_type === 'day' ? $psp->day_worker_target : $psp->night_worker_target;

        $AttendanceSummaryService = new AttendanceSummaryService();
        $total_count = $AttendanceSummaryService->countActiveDayNight($psp->id, date('Y-m-d'));
        $currentCount = $shift->shift_type === 'day' ? $total_count['total_day'] : $total_count['total_night'];

        return $currentCount >= $limit;
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive', 'PSP'])) {
            $data = [
                "pageTitle" => "View Worker",
                "worker" => $worker,
            ];
            return view("workers.role_psp.view", $data);
        }

        dd("Coming soon ");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $shifts = Shift::all();
        if ($user->hasRole('PSP')) {
            $data = [
                "pageTitle" => "Edit Worker",
                "worker" => $worker,
                "shifts" => $shifts,
                "user" => $user,
            ];
            return view("workers.role_psp.edit", $data);
        }

        // $worker = $worker->with(['revisions.user' => function ($query) {
        //     $query->orderBy('created_at', 'desc'); // Sort revisions by created_at descending
        // }]);
        $revisions = $worker->revisions->sortByDesc('created_at');
        // dd($revisions);

        // if ($worker->revisions->isNotEmpty()) {
        //     // foreach ($worker->revisions as $revision) {
        //     dd($worker->revisions->toArray());
        //     // }
        // }

        if ($user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
            $psps = Psp::all();
            $data = [
                "pageTitle" => "Edit Worker",
                "worker" => $worker,
                "psps" => $psps,
                "shifts" => $shifts,
                "revisions" => $revisions,
            ];
            return view("workers.edit", $data);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkerRequest $request, Worker $worker): RedirectResponse
    {
        $this->workerService->updateWorker($request, $worker);
        return redirect()->route('workers.index')->with('success', 'Worker updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        $worker->delete();
        return redirect()->route('workers.index')->with('success', 'Worker deleted successfully');
    }


    public function showImportForm(Request $request)
    {
        $data = [
            "pageTitle" => "Import Workers",
        ];
        return view('workers.import', $data); // View to upload CSV
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);
        try {
            Excel::import(new WorkersImport, $request->file('file'));
            return redirect()->back()->with('success', 'Workers imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function filter(Request $request)
    {
        $query = Worker::query();

        // Filter workers by division
        if ($request->division) {
            $query->whereHas('divisions', function ($q) use ($request) {
                $q->where('id', $request->division);
            });
        }

        // Get workers and map them with assignment status
        $workers = $query->get()->map(function ($worker) use ($request) {
            return [
                'id' => $worker->id,
                'name' => $worker->name,
                'division' => $worker->division,
                'assigned' => $worker->holidays->contains('id', $request->holiday_id ?? 0), // Check if worker is assigned to the holiday
            ];
        });

        return response()->json($workers);
    }

    public function deleted()
    {
        $pageTitle = "Import Workers";
        $deletedWorkers = Worker::onlyTrashed()->with('deleter')->paginate(10);  // Fetch deleted workers

        return view('workers.deleted', compact('pageTitle', 'deletedWorkers'));
    }

    /**
     * Restore a deleted worker.
     */
    public function restore($id)
    {
        $worker = Worker::onlyTrashed()->findOrFail($id);

        $worker->restore(); // Restore the worker

        return redirect()->route('workers.deleted')->with('success', 'Worker restored successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editLongForm(Worker $worker)
    {
        $pageTitle = "Long Form Edit Worker";
        $workerTypes = Workertype::all();
        $workingareas = WorkingArea::all();
        $designations = Designation::all();
        $productionProducts = ProductionProduct::all();
        $levels = Level::all();
        $religionsArray = [
            [
                'id' => 'Hindu',
                'name' => 'Hindu'
            ],
            [
                'id' => 'Muslim',
                'name' => 'Muslim'
            ],
            [
                'id' => 'Christian',
                'name' => 'Christian'
            ],
            [
                'id' => 'Sikh',
                'name' => 'Sikh'
            ],
            [
                'id' => 'Jain',
                'name' => 'Jain'
            ],
            [
                'id' => 'Budhist',
                'name' => 'Budhist'
            ],
        ];
        $religions = collect($religionsArray)->map(function ($item) {
            return (object)$item;
        });

        $maritalStatusesArray = [
            [
                'id' => 'Unmarried',
                'name' => 'Unmarried'
            ],
            [
                'id' => 'Married',
                'name' => 'Married'
            ],
            [
                'id' => 'Divorced',
                'name' => 'Divorced'
            ],
            [
                'id' => 'Widowed',
                'name' => 'Widowed'
            ],
        ];
        $maritalStatuses = collect($maritalStatusesArray)->map(function ($item) {
            return (object)$item;
        });

        $divisions = Division::with('departments.subdepartments.lines.processes')->get();

        return view(
            "workers.edit-long-form",
            compact(
                'pageTitle',
                'worker',
                'workerTypes',
                'designations',
                'workingareas',
                'productionProducts',
                'levels',
                'religions',
                'maritalStatuses',
                'divisions',
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function updateLongForm(UpdateWorkerLongFormRequest $request, Worker $worker)
    {
        $this->workerService->updateWorkerLongForm($request, $worker);

        return redirect()->back()->with('success', 'Worker long form updated successfully.');
    }
}
