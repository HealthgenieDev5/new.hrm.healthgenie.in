<?php

namespace App\Http\Controllers\Overrides;

use App\Http\Controllers\Controller;

use App\Models\Shift;
use App\Models\ShiftOverride;
use App\Models\Worker;
use Illuminate\Http\Request;

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

        // Check if the worker already has an overlapping shift override
        $overlappingShift = ShiftOverride::where('worker_id', $validated['worker_id'])
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
                    'end_date' => 'This worker already has a shift override in the selected date range.'
                ]
            )->withInput();
        }

        ShiftOverride::create($validated);

        return redirect()->route('overrides.shiftoverride.index')->with('success', 'Shift Override created successfully.');
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
                    'end_date' => 'This worker already has a shift override in the selected date range.'
                ]
            )->withInput();
        }


        $shiftoverride->update($validated);

        return redirect()->route('overrides.shiftoverride.index')->with('success', 'Shift Override updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShiftOverride $shiftoverride)
    {
        $shiftoverride->delete();

        return redirect()->route('overrides.shiftoverride.index')->with('success', 'Shift Override deleted successfully.');
    }
}
