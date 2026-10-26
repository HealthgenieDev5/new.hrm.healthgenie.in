<?php

namespace App\Http\Controllers\Overrides;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\ShiftOverride;
use App\Models\Worker;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ShiftOverridesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Manage Shift Overrides';
        $workers = Worker::all();

        $shiftoverrides = ShiftOverride::when(request('worker_id'), function ($query, $workerId) {
            return $query->where('worker_id', $workerId);
        })
            ->paginate(10)
            ->appends($request->query());

        return view('overrides.shift.index', compact('shiftoverrides', 'pageTitle', 'workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'New Shift Override';
        $workers = Worker::all();
        $shifts = Shift::all();

        return view('overrides.shift.create', compact('pageTitle', 'workers', 'shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createMultiple()
    {
        $pageTitle = 'Bulk Shift Override';
        $workers = Worker::all();
        $shifts = Shift::all();

        return view('overrides.shift.create-multiple', compact('pageTitle', 'workers', 'shifts'));
    }

    /**
     * Get shift overrides datatable for a specific worker
     */
    public function getShiftOverrides(Request $request)
    {
        if ($request->ajax()) {
            $workerId = $request->get('worker_id');

            $shiftOverrides = ShiftOverride::with(['worker', 'shift'])
                ->where('worker_id', $workerId)
                ->orderBy('start_date', 'desc')
                ->get();

            return DataTables::of($shiftOverrides)
                ->addColumn('worker_name', function ($row) {
                    return $row->worker->name . ' (' . $row->worker->code . ')';
                })
                ->addColumn('shift_name', function ($row) {
                    return $row->shift->name;
                })
                ->editColumn('start_date', function ($row) {
                    return date('d M Y', strtotime($row->start_date));
                })
                ->editColumn('end_date', function ($row) {
                    return date('d M Y', strtotime($row->end_date));
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="' . route('overrides.shiftoverride.edit', $row->id) . '" class="btn btn-icon btn-sm btn-light-primary">
                        <i class="fa fa-edit"></i>
                    </a>';

                    $deleteBtn = '<button type="button" class="btn btn-icon btn-sm btn-light-danger delete-shift-override" data-id="' . $row->id . '" data-url="' . route('overrides.shiftoverride.destroy', $row->id) . '">
                        <i class="fa fa-trash"></i>
                    </button>';

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        if ($this->hasOverlap($validated['worker_id'], $validated['start_date'], $validated['end_date'])) {
            return redirect()->back()->withErrors([
                'start_date' => 'This worker already has a shift override in the selected date range.',
                'end_date' => 'This worker already has a shift override in the selected date range.',
            ])->withInput();
        }

        $this->performSave($validated);

        return redirect()->back()->with('success', 'Shift Override created successfully.')->withInput();
    }

    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'worker_id'   => 'required|array',
            'worker_id.*' => 'exists:workers,id',
            'shift_id'    => 'required|exists:shifts,id',
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date'],
        ]);

        $errors = [];

        # update the array to overriede in bulk if you do not want to spend time selecting workers, just paste their codes in the array and uncomment from line number 134 to 137
        // $codeArray = ['AK1049', 'AK1048', 'AK1047', 'AK1046', 'AK1045', 'AK1044', 'AK1040', 'AK1039', 'AK1038', 'AK1037', 'AK1036', 'AK1035', 'AK1034', 'AK1033', 'AK1032', 'AK1031', 'AK1030', 'AK1029', 'AK1026', 'AK1025', 'AK1019', 'AK1011', 'AK1003', 'AK1001', 'AK996', 'AK994', 'AK990', 'AK988', 'AK987', 'AK985', 'AK983', 'AK968', 'AK963', 'AK958', 'AK956', 'AK950', 'AK949', 'AK944', 'AK943', 'AK934', 'AK919', 'AK918', 'AK903', 'AK902', 'AK900', 'AK890', 'AK880', 'AK859', 'AK856', 'AK849', 'AK848', 'AK839', 'AK820', 'AK818', 'AK817', 'AK811', 'AK808', 'AK801', 'AK798', 'AK751', 'AK749', 'AK730', 'AK729', 'AK686', 'AK652', 'AK619', 'AK588', 'AK587', 'AK584', 'AK499', 'AK489', 'AK473', 'AK400', 'AK378', 'AK343', 'AK342', 'AK286', 'AS22', 'AS21', 'DS496', 'DS495', 'DS494', 'DS493', 'DS492', 'DS491', 'DS490', 'DS489', 'DS488', 'DS487', 'DS486', 'DS485', 'DS484', 'DS483', 'DS482', 'DS481', 'DS480', 'DS479', 'DS477', 'DS476', 'DS467', 'DS465', 'DS464', 'DS463', 'DS462', 'DS461', 'DS437', 'DS435', 'DS397', 'DS343', 'DS342', 'DS340', 'DS290', 'DS244', 'DS112', 'DS69', 'DS49', 'DS1', 'PE633', 'PE632', 'PE631', 'PE630', 'PE629', 'PE575', 'PE565', 'PE547', 'PE539', 'PE492', 'PE447', 'PE139', 'RS552', 'RS551', 'RS548', 'RS547', 'RS546', 'RS538', 'RS524', 'RS496', 'RS455', 'RS435', 'RS419', 'RS380', 'RS376', 'RS371', 'RS302', 'RS079', 'RS083', 'RS089', 'RS101', 'RS121', 'RS188', 'BL587', 'BL585', 'BL582', 'BL581', 'BL579', 'BL578', 'BL557', 'BL548', 'BL538', 'BL533', 'BL528', 'BL526', 'BL525', 'BL502', 'BL500', 'BL496', 'BL488', 'BL469', 'BL439', 'BL438', 'BL420', 'BL390', 'BL322', 'BL318', 'BL314', 'BL272', 'BL273', 'BL257', 'BL004', 'BL014', 'BL015'];
        // $codeArray = ['HN1088', 'HN978', 'HN980', 'HN959', 'HN1134', 'HN1138', 'HN985', 'HN955', 'HN1099', 'HN960', 'HN1060', 'HN1096', 'HN1053', 'HN968', 'HN1029', 'HN956', 'HN988', 'HN981', 'HN957', 'HN961', 'HN947', 'HN948', 'HN850', 'HN132', 'HN991', 'HN958', 'HN1111', 'HN852', 'HN851', 'HN1098', 'HN953', 'HN964', 'HN871', 'HN941', 'HN121', 'HN1126', 'HN936', 'HN862', 'HN935', 'HN937', 'HN21', 'HN866', 'HN841', 'HN141'];
        // $codeArray = ['AK1076', 'AK1075', 'AK1074', 'AK1073', 'AK1072', 'AK1071', 'AK1070', 'AK1069', 'AK1068', 'AK1067', 'AK1066', 'AK1065', 'AK1064', 'AK1063', 'AK1062', 'AK1061', 'AK1060', 'AK1059', 'AK1058', 'AK1057', 'AK1056', 'AK1055', 'AK1054', 'AK1053', 'AK1052', 'AK1051', 'AK1046', 'AK1045', 'AK1044', 'AK1040', 'AK1032', 'AK1031', 'AK1030', 'AK1019', 'AK1003', 'AK990', 'AK988', 'AK985', 'AK983', 'AK959', 'AK958', 'AK950', 'AK949', 'AK902', 'AK900', 'AK890', 'AK884', 'AK880', 'AK856', 'AK817', 'AK786', 'AK729', 'AK686', 'AK652', 'AK619', 'AK588', 'AK584', 'AK489', 'AK473', 'AK400', 'AK378', 'RS580', 'RS579', 'RS578', 'RS577', 'RS576', 'RS575', 'RS574', 'RS573', 'RS572', 'RS571', 'RS570', 'RS569', 'RS568', 'RS567', 'RS566', 'RS565', 'RS564', 'RS563', 'RS562', 'RS561', 'RS560', 'RS559', 'RS558', 'RS557', 'RS556', 'RS555', 'RS554', 'RS553', 'RS546', 'RS538', 'RS489', 'RS435', 'RS419', 'RS417', 'RS376', 'RS089', 'RS177', 'AS51', 'AS50', 'AS49', 'AS48', 'AS47', 'BL596', 'BL595', 'BL594', 'BL579', 'BL578', 'BL528', 'BL526', 'BL502', 'BL496', 'BL469', 'BL438', 'BL273', 'BL272', 'BL257', 'BL004', 'BL014', 'DS463', 'DS435', 'DS340', 'DS112', ''];
        // $workers = Worker::whereIn('code', $codeArray)->get();
        // $workerIds = $workers->pluck('id')->toArray();
        // $validated['worker_id'] = $workerIds;

        // dd($validated);

        foreach ($validated['worker_id'] as $workerId) {
            if ($this->hasOverlap($workerId, $validated['start_date'], $validated['end_date'])) {
                $worker = Worker::find($workerId);
                $errors[] = "Worker {$worker->name} ({$worker->code}) already has a shift override in the selected date range.";
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors(['overlap' => $errors])->withInput();
        }
        // dd('We will come back');
        foreach ($validated['worker_id'] as $workerId) {
            $this->performSave([
                'worker_id'  => $workerId,
                'shift_id'   => $validated['shift_id'],
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'],
            ]);
        }

        return redirect()->back()->with('success', 'Shift Overrides created successfully.')->withInput();
    }

    private function hasOverlap(int $workerId, string $startDate, string $endDate): bool
    {
        return ShiftOverride::where('worker_id', $workerId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }

    private function performSave(array $data): void
    {
        ShiftOverride::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShiftOverride $shiftoverride)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShiftOverride $shiftoverride)
    {
        $pageTitle = 'Edit Shift Override';
        $workers = Worker::all();
        $shifts = Shift::all();

        return view('overrides.shift.edit', compact('shiftoverride', 'pageTitle', 'workers', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShiftOverride $shiftoverride)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        // Check if the worker already has an overlapping shift override
        $overlappingShift = ShiftOverride::where('worker_id', $validated['worker_id'])
            ->where('id', '!=', $shiftoverride->id) // Exclude the current record from the check
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlappingShift) {
            return redirect()->back()->withErrors(
                [
                    'start_date' => 'This worker already has a shift override in the selected date range.',
                    'end_date' => 'This worker already has a shift override in the selected date range.',
                ]
            )->withInput();
        }

        $shiftoverride->update($validated);

        return redirect()->route('overrides.shiftoverride.index')->with('success', 'Shift Override updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ShiftOverride $shiftoverride)
    {
        $shiftoverride->delete();

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Shift Override deleted successfully.',
            ]);
        }

        return redirect()->route('overrides.shiftoverride.index')->with('success', 'Shift Override deleted successfully.');
    }
}
