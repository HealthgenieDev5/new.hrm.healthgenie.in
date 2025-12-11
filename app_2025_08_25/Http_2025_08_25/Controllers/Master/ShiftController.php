<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Shift;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Shifts';
        $shifts = Shift::paginate(10);
        return view('master.shifts.index', compact('shifts', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Shift';
        $shifts = Shift::all();
        return view('master.shifts.create', compact('pageTitle', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:shifts,name'],
            'in_time' => ['required', 'date_format:H:i'],
            'out_time' => ['required', 'date_format:H:i'],
            'week_off' => ['required', Rule::in(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])],
            'sub_type' => ['required', Rule::in(['regular', 'reduced'])],
            'sister_shift' => ['nullable', 'exists:shifts,id'],
            'grace_minutes' => ['required', 'date_format:H:i'],
            'is_internal' => 'required|boolean',
        ]);

        // Parse in_time and out_time
        $inTime = Carbon::createFromFormat('H:i', $request->in_time);
        $outTime = Carbon::createFromFormat('H:i', $request->out_time);

        $shiftType = $outTime->isBefore($inTime) ? 'night' : 'day';
        $validated['shift_type'] = $shiftType;

        Shift::create($validated);

        return redirect()->route('master.shifts.index')->with('success', 'Shift created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        $pageTitle = 'Edit Shift';
        $shifts = Shift::all();
        return view('master.shifts.edit', compact('shift', 'pageTitle', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:shifts,name,' . $shift->id],
            'in_time' => ['required', 'date_format:H:i'],
            'out_time' => ['required', 'date_format:H:i'],
            'week_off' => ['required', Rule::in(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])],
            'sub_type' => ['required', Rule::in(['regular', 'reduced'])],
            'sister_shift' => ['nullable', 'exists:shifts,id', Rule::notIn([$shift->id])],
            'grace_minutes' => ['required', 'date_format:H:i'],
            'is_internal' => 'required|boolean',
        ]);

        // dd([$shift->id, $validated['sister_shift']]);

        // Parse in_time and out_time
        $inTime = Carbon::createFromFormat('H:i', $request->in_time);
        $outTime = Carbon::createFromFormat('H:i', $request->out_time);

        $shiftType = $outTime->isBefore($inTime) ? 'night' : 'day';
        $validated['shift_type'] = $shiftType;

        // dd($validated);

        $shift->update($validated);

        return redirect()->route('master.shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('master.shifts.index')->with('success', 'Shift deleted successfully.');
    }
}
