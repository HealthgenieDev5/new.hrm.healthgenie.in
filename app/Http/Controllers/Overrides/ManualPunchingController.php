<?php

namespace App\Http\Controllers\Overrides;

use App\Http\Controllers\Controller;

use App\Models\ManualPunching;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManualPunchingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Manual Punching';
        $workers = Worker::all();

        $manualPunchings = ManualPunching::when(request('worker_id'), function ($query, $workerId) {
            return $query->where('worker_id', $workerId);
        })
            ->paginate(10)
            ->appends($request->query());


        return view('overrides.manualpunching.index', compact('manualPunchings', 'pageTitle', 'workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Add Manual Punch';
        $workers = Worker::all();
        return view('overrides.manualpunching.create', compact('pageTitle', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_id'   => 'required|exists:workers,id',
            'shift_type'  => 'required|in:day,night',
            'date'        => 'required|date',
            'intime'      => 'nullable|date_format:H:i',
            'outtime'     => 'nullable|date_format:H:i',
            'remarks'     => 'required',
        ]);

        $exists = ManualPunching::where('worker_id', $request->worker_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            $validator->after(function ($validator) {
                $validator->errors()->add('date', 'Record already exists for that worker on this date.');
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $manualPunching = new ManualPunching();
        $manualPunching->worker_id  = $request->worker_id;
        $manualPunching->shift_type = $request->shift_type;
        $manualPunching->date       = $request->date;
        $manualPunching->intime     = $request->intime;
        $manualPunching->outtime    = $request->outtime;
        $manualPunching->remarks    = $request->remarks;
        $manualPunching->save();

        return redirect()->route('overrides.manualpunching.index')
            ->with('success', 'Manual punching created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ManualPunching $manualPunching)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ManualPunching $manualpunching)
    {
        // dd($manualpunching);
        $manualPunching = $manualpunching;
        $pageTitle = 'Edit Manual Punching';
        $workers = Worker::all();
        return view('overrides.manualpunching.edit', compact('manualPunching', 'pageTitle', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ManualPunching $manualpunching)
    {
        $manualPunching = $manualpunching;

        $validator = Validator::make($request->all(), [
            'worker_id'   => 'required|exists:workers,id',
            'shift_type'  => 'required|in:day,night',
            'date'        => 'required|date',
            'intime'      => 'nullable|date_format:H:i',
            'outtime'     => 'nullable|date_format:H:i',
            'remarks'     => 'required',
        ]);

        $exists = ManualPunching::where('worker_id', $request->worker_id)
            ->where('date', $request->date)
            ->where('id', '!=', $manualPunching->id)
            ->exists();

        if ($exists) {
            $validator->after(function ($validator) {
                $validator->errors()->add('date', 'Another record already exists for that worker on this date.');
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $manualPunching->worker_id  = $request->worker_id;
        $manualPunching->shift_type = $request->shift_type;
        $manualPunching->date       = $request->date;
        $manualPunching->intime     = $request->intime;
        $manualPunching->outtime    = $request->outtime;
        $manualPunching->remarks    = $request->remarks;

        $manualPunching->save();
        return redirect()->route('overrides.manualpunching.index')
            ->with('success', 'Manual punching updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManualPunching $manualpunching)
    {
        $manualpunching->delete();

        return redirect()->route('overrides.manualpunching.index')->with('success', 'Manual Punch deleted successfully.');
    }
}
