<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Http\Requests\StorePlanningRequest;
use App\Http\Requests\UpdatePlanningRequest;
use App\Interfaces\ProductionPlanningInterface;
use App\Models\Holiday;
use App\Models\Line;
use App\Models\LineRequirement;
use App\Models\ProcessedAttendance;
use App\Models\Shift;
use App\Models\ShiftOverride;
use App\Models\TemporaryLineAssignment;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlanningController extends Controller
{

    protected $planner;

    public function __construct(ProductionPlanningInterface $planner)
    {
        $this->planner = $planner;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = "Production Planning";
        $holidays = Holiday::select('date')->get()->toArray();
        $holidays = array_column($holidays, 'date');
        $month = $request->input('month') ?? now()->format('Y-m');
        $plans = $this->planner->getPlansForMonthPivoted($month);

        $shiftCounts = [];
        foreach ($plans['dates'] as $date) {
            $shiftCounts[$date] = $this->planner->getShiftWiseCountsForDate($date);
        }

        $displayNames = [
            'total' => 'Required Total',
            'workers' => 'Required Workers',
            'extras' => 'Required Extras',
            'supervisors' => 'Required Supervisors',
            'qas' => 'Required Qas',
            'services' => 'Required Services',
            'movers' => 'Required Movers',
            'assigned' => 'Assigned',
            'present' => 'Present',
            'shortfall' => 'Shortfall/Excess',
            'status' => 'Status',
        ];
        $totalSums = [];
        foreach ($displayNames as $param => $label) {
            foreach ($plans['dates'] as $date) {
                $totalSums[$param][$date]['day'] = 0;
                $totalSums[$param][$date]['night'] = 0;
            }
        }
        foreach ($plans['data'] as $line => $parameters) {
            foreach ($displayNames as $param => $label) {
                if ($param === 'status') continue; // Already handled above
                foreach ($plans['dates'] as $date) {
                    $val = $parameters[$param][$date] ?? ['day' => 0, 'night' => 0];
                    $totalSums[$param][$date]['day'] += $val['day'] ?? 0;
                    $totalSums[$param][$date]['night'] += $val['night'] ?? 0;
                }
            }
        }

        $unorderedData = collect($plans['data']);
        $orderedKeys = [
            "IVC 1",
            "IVC 2",
            "IVC Common",
            "Tubing 1",
            "Tubing 2",
            "QA",
            "KC Tubing",
            "PCKG : Hard Blister",
            "PCKG : Soft Blister",
            "Final Inspection & Packing",
            "Extrusion",
            "MVA Assembly",
            "Stores",
            "FG Store",
            "Maintainance",
            "Housekeeping",
            "Resuscitator",
            "Automation",
            "Moulding",
            "DND",
            "Orthoaids",
            "Silicon",
            "Services and Others",
            "Others Production",
        ];
        $reorderedData = collect();
        // Add prioritized ones first
        foreach ($orderedKeys as $key) {
            if ($unorderedData->has($key)) {
                $reorderedData[$key] = $unorderedData[$key];
                $unorderedData->forget($key);
            }
        }
        // Add remaining ones
        $reorderedData = $reorderedData->merge($unorderedData);
        // dd([$plans['data'], $reorderedData]);

        $plans['data'] = $reorderedData;
        $linesByName = Line::all()->keyBy('name');


        return view('planning.defaults.index', [
            'pageTitle' => $pageTitle,
            'month' => $month,
            'plans' => $plans['data'],
            'dates' => $plans['dates'],
            'holidays' => $holidays,
            'shiftCounts' => $shiftCounts,
            'totalSums' => $totalSums,
            'displayNames' => $displayNames,
            'linesByName' => $linesByName,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $pageTitle = "New Plan";
        $existingRequirement = null;
        $processAssignments = collect(); // Initialize
        $perProcessSummary = collect();  // Initialize
        $targetDate = $request->input('date') ? Carbon::parse($request->input('date'))->toDateString() : now()->toDateString();

        if ($request->filled('line_id')) {
            $line_id = $request->input('line_id');
            $line = Line::findOrFail((int) $line_id);
            if (!$line) {
                abort('404', "Line not found");
            }
            $shiftWiseWorkers = $this->planner->getShiftWiseWorkers($line_id, $targetDate);

            $assignedDayWorkers = $shiftWiseWorkers['day'];
            $assignedNightWorkers = $shiftWiseWorkers['night'];
            $availableWorkers = $shiftWiseWorkers['availableWorkers'];
            $attendanceStatus = ProcessedAttendance::whereDate('date', $targetDate)
                ->whereIn('worker_id', $assignedDayWorkers->merge($assignedNightWorkers)->merge($availableWorkers)->pluck('id'))
                ->pluck('status', 'worker_id');

            $assignedDayWorkers->each(function ($w) use ($attendanceStatus) {
                $w->attendance_status = $attendanceStatus[$w->id] ?? null;
            });

            $assignedNightWorkers->each(function ($w) use ($attendanceStatus) {
                $w->attendance_status = $attendanceStatus[$w->id] ?? null;
            });

            $availableWorkers->each(function ($w) use ($attendanceStatus) {
                $w->attendance_status = $attendanceStatus[$w->id] ?? null;
            });

            $presentDayCount = $this->planner->getPresentCount($assignedDayWorkers->pluck('id'), $targetDate);
            $presentNightCount = $this->planner->getPresentCount($assignedNightWorkers->pluck('id'), $targetDate);

            $shifts = Shift::all()->keyBy('id');
            $processes = $line->processes;

            // Fetch all assignments for this line and today
            $assignments = \App\Models\TemporaryLineAssignment::with(['worker', 'process', 'shift'])
                ->where('line_id', $line_id)
                ->whereDate('start_date', '<=', $targetDate)
                ->whereDate('end_date', '>=', $targetDate)
                ->get();

            // Attach temp process to each worker if exists
            $tempAssignments = $assignments->keyBy('worker_id');
            $assignedDayWorkers->each(function ($w) use ($tempAssignments) {
                if ($tempAssignments->has($w->id)) {
                    $w->temp_process = $tempAssignments[$w->id]->process;
                }
            });
            $assignedNightWorkers->each(function ($w) use ($tempAssignments) {
                if ($tempAssignments->has($w->id)) {
                    $w->temp_process = $tempAssignments[$w->id]->process;
                }
            });

            // Group by process_id, then by shift_type
            $processAssignments = $assignments->groupBy(function ($a) {
                return $a->process_id;
            })->map(function ($group) {
                return $group->groupBy(function ($a) {
                    return $a->shift->shift_type;
                });
            });

            // Group workers by their effective process (considering temporary assignments)
            $groupedDayWorkers = $assignedDayWorkers->groupBy(function ($w) use ($tempAssignments) {
                return $tempAssignments->has($w->id) ? $tempAssignments[$w->id]->process_id : $w->process_id;
            });

            $groupedNightWorkers = $assignedNightWorkers->groupBy(function ($w) use ($tempAssignments) {
                return $tempAssignments->has($w->id) ? $tempAssignments[$w->id]->process_id : $w->process_id;
            });

            $perProcessSummary = collect();
            foreach ($processes as $process) {
                // Day shift
                $dayWorkers = $groupedDayWorkers->get($process->id, collect()); // Get pre-grouped workers, default to empty collection
                $presentDay = $dayWorkers->filter(fn($w) => in_array($w->attendance_status, ['P', 'M/P']))->count();

                // Night shift
                $nightWorkers = $groupedNightWorkers->get($process->id, collect()); // Get pre-grouped workers, default to empty collection
                $presentNight = $nightWorkers->filter(fn($w) => in_array($w->attendance_status, ['P', 'M/P']))->count();

                $perProcessSummary[$process->id] = [
                    'day' => [
                        'assigned' => $dayWorkers->count(),
                        'present' => $presentDay,
                        'workers' => $dayWorkers, // Keep the collection of workers
                    ],
                    'night' => [
                        'assigned' => $nightWorkers->count(),
                        'present' => $presentNight,
                        'workers' => $nightWorkers,
                    ],
                ];
            }

            $existingRequirement = [
                'line_name' => $line->name,
                'line_id'   => $line_id,
                'day'       => $this->planner->getRequirementForShift($line_id, $targetDate, 'day'),
                'night'     => $this->planner->getRequirementForShift($line_id, $targetDate, 'night'),
                'assignedDayWorkers' => $assignedDayWorkers,
                'assignedNightWorkers' => $assignedNightWorkers,
                'availableWorkers' => $availableWorkers,
                'presentDayCount' => $presentDayCount,
                'presentNightCount' => $presentNightCount,
                'shifts' => $shifts,
                'processes' => $processes,
            ];
        }

        // Pass targetDate to the view so it can be included in the form submission
        return view("planning.defaults.create", compact('pageTitle', 'existingRequirement', 'processAssignments', 'perProcessSummary', 'targetDate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanningRequest $request)
    {
        $validated = $request->validated();
        $validated['date'] = now()->toDateString();

        // Assuming StorePlanningRequest is updated to include 'date' field
        // And it's validated. If not, you might need to get it from $request directly
        // For now, let's assume 'date' is part of $validated.

        $base = [
            'line_id' => $validated['line_id'],
            'date' => $validated['date'],
        ];

        $this->planner->createOrUpdateRequirement(array_merge($base, $validated['day'], ['shift_type' => 'day']));
        $this->planner->createOrUpdateRequirement(array_merge($base, $validated['night'], ['shift_type' => 'night']));

        return redirect()->route('production.planning.index', ['month' => Carbon::parse($validated['date'])->format('Y-m')])
            ->with('success', 'Line requirement saved successfully for ' . $validated['date'] . '!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Planning $planning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Planning $planning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanningRequest $request, Planning $planning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Planning $planning)
    {
        //
    }

    public function assignToLine(Request $request)
    {

        $request->validate([
            'worker_ids'   => 'required|array',
            'worker_ids.*' => 'exists:workers,id',
            'line_id'      => 'required|exists:lines,id',
            'shift_id'     => 'required|exists:shifts,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'process_id'   => 'nullable|exists:processes,id',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        foreach ($request->worker_ids as $workerId) {
            TemporaryLineAssignment::updateOrCreate(
                [
                    'worker_id'  => $workerId,
                    'line_id'    => $request->line_id,
                    'start_date' => $request->start_date,
                    'end_date'   => $request->end_date,
                ],
                [
                    'shift_id'   => $request->shift_id,
                    'process_id' => $request->process_id,
                ]
            );

            // 2. Find any overlapping ShiftOverride
            $existing = ShiftOverride::where('worker_id', $workerId)
                ->where('start_date', '<=', $end)
                ->where('end_date', '>=', $start)
                ->get();

            foreach ($existing as $override) {
                // Case: new override is fully inside an existing one
                if ($override->start_date < $start && $override->end_date > $end) {
                    // Split into three parts

                    // Before range
                    ShiftOverride::create([
                        'worker_id'  => $workerId,
                        'shift_id'   => $override->shift_id,
                        'start_date' => $override->start_date,
                        'end_date'   => $start->copy()->subDay(),
                    ]);

                    // After range
                    ShiftOverride::create([
                        'worker_id'  => $workerId,
                        'shift_id'   => $override->shift_id,
                        'start_date' => $end->copy()->addDay(),
                        'end_date'   => $override->end_date,
                    ]);

                    // Delete original
                    $override->delete();
                }

                // Case: overlap at beginning
                elseif ($override->start_date < $start && $override->end_date >= $start && $override->end_date <= $end) {
                    $override->update([
                        'end_date' => $start->copy()->subDay(),
                    ]);
                }

                // Case: overlap at end
                elseif ($override->end_date > $end && $override->start_date >= $start && $override->start_date <= $end) {
                    $override->update([
                        'start_date' => $end->copy()->addDay(),
                    ]);
                }

                // Case: completely inside new override → remove
                elseif ($override->start_date >= $start && $override->end_date <= $end) {
                    $override->delete();
                }
            }

            // 3. Insert the new shift override
            ShiftOverride::create([
                'worker_id'  => $workerId,
                'shift_id'   => $request->shift_id,
                'start_date' => $start,
                'end_date'   => $end,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function removeFromLine(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'line_id' => 'required|exists:lines,id',
        ]);

        $today = now()->toDateString();

        // Get active or future assignment
        $assignment = TemporaryLineAssignment::where('worker_id', $request->worker_id)
            ->where('line_id', $request->line_id)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date', 'desc') // latest assignment
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'No active assignment found.'], 404);
        }

        if ($assignment->start_date > $today) {
            // Future assignment → delete it
            $assignment->delete();
            return response()->json(['message' => 'Assignment deleted.']);
        }

        if ($assignment->start_date == $today) {
            // Starts today → also delete
            $assignment->delete();
            return response()->json(['message' => 'Assignment deleted from today.']);
        }

        if ($assignment->start_date < $today && $assignment->end_date >= $today) {
            // Already started → shorten to yesterday
            $assignment->end_date = now()->subDay()->toDateString();
            $assignment->save();

            return response()->json(['message' => 'Assignment shortened to exclude today onward.']);
        }

        // If already ended in past — no action
        return response()->json(['message' => 'Assignment is already in the past.'], 200);
    }

    public function exportAssignedWorkers(Request $request)
    {
        // dd('Reached Export Method');

        $request->validate([
            'line_id' => 'required|exists:lines,id'
        ]);

        $lineId = (int) $request->line_id;
        $line = Line::findOrFail($lineId);

        $workers = $this->planner->getShiftWiseWorkers($lineId, now()->toDateString());

        $dayWorkers = $workers['day'];
        $nightWorkers = $workers['night'];

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="line-workers-' . $lineId . '-' . now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($line, $dayWorkers, $nightWorkers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Line name', 'Day shift', 'Night shift', 'Worker name', 'Code', 'Psp', 'Level', 'permanently assigned to', 'temporarily assigned to']);

            foreach ($dayWorkers as $worker) {
                $permanent = $worker->line?->name ?? '';
                $temporary = $worker->line_id == $line->id ? '' : $line->name;
                fputcsv($handle, [$line->name, 'Yes', '', $worker->name, $worker->code, $worker->psp->name, $worker?->level?->name, $permanent, $temporary]);
            }

            foreach ($nightWorkers as $worker) {
                $permanent = $worker->line?->name ?? '';
                $temporary = $worker->line_id == $line->id ? '' : $line->name;
                fputcsv($handle, [$line->name, '', 'Yes', $worker->name, $worker->code, $worker->psp->name, $worker?->level?->name, $permanent, $temporary]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function getProcessesForLine($lineId)
    {
        $line = Line::findOrFail($lineId);
        $processes = $line->processes()->select('id', 'name')->get();
        return $processes;
    }

    /**
     * Set requirements to zero for a line and shift for all dates in the current month.
     */
    public function closeShift(Request $request)
    {
        $request->validate([
            'line_id' => 'required|exists:lines,id',
            'shift_type' => 'required|in:day,night',
        ]);

        $lineId = $request->input('line_id');
        $shiftType = $request->input('shift_type');
        $date = date('Y-m-d');

        try {
            $this->planner->createOrUpdateRequirement([
                'line_id' => $lineId,
                'date' => $date,
                'shift_type' => $shiftType,
                'workers' => 0,
                'extras' => 0,
                'supervisors' => 0,
                'qas' => 0,
                'services' => 0,
                'movers' => 0,
            ]);
            return redirect()->back()->with('success', 'Requirements set to zero for ' . ucfirst($shiftType) . ' shift on ' . $date . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to close shift: ' . $e->getMessage());
        }
    }
}
