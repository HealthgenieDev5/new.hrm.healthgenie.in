<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Holiday;
use App\Models\Worker;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Holidays';
        $holidays = Holiday::paginate(10);

        return view('master.holidays.index', compact('holidays', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Holiday';

        return view('master.holidays.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
            'code' => ['required', 'string', 'in:HL,NH,RH,SPL HL'],
        ]);

        Holiday::create($validated);

        return redirect()->route('master.holidays.index')->with('success', 'Holiday created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holiday $holiday)
    {
        $pageTitle = 'Edit Holiday';
        $divisions = Division::all();
        $workers = collect();

        // Only fetch workers for SPL HL holidays
        if ($holiday->code == 'SPL HL') {
            // Fetch active workers grouped by division
            $workers = Worker::active()
                ->with('division')
                ->orderBy('division_id')
                ->orderBy('name')
                ->get()
                ->groupBy(fn ($worker) => $worker->division_id ?? 0);

            // Reorder to put "Other" (division_id = 0) at the end
            if ($workers->has(0)) {
                $other = $workers->get(0);
                $workers->forget(0);
                $workers->put(0, $other);
            }
        }

        return view('master.holidays.edit', compact('holiday', 'pageTitle', 'divisions', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date,'.$holiday->id,
            'code' => ['required', 'string', 'in:HL,NH,RH,SPL HL'],
            'workers' => ['nullable', 'array'], // Validate workers as an array
            'workers.*' => ['exists:workers,id'], // Ensure each worker ID exists in the workers table
        ]);

        $holiday->update($validated);

        $holiday->workers()->sync($validated['workers'] ?? []);
        // $holiday->workers()->syncWithoutDetaching($validated['workers'] ?? []);

        return redirect()->route('master.holidays.index')->with('success', 'Holiday updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('master.holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
