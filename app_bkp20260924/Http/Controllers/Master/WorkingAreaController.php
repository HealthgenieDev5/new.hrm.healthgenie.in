<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\WorkingArea;
use App\Http\Requests\StoreWorkingAreaRequest;
use App\Http\Requests\UpdateWorkingAreaRequest;

class WorkingAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Working Area';
        $workingAreas = WorkingArea::paginate(10);
        return view('master.working-area.index', compact('workingAreas', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Working Area';
        return view('master.working-area.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkingAreaRequest $request)
    {
        WorkingArea::create($request->validated());
        return redirect()->route('master.working-area.index')->with('success', 'Working area created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkingArea $workingArea)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkingArea $workingArea)
    {
        $pageTitle = 'Edit Working Area';
        return view('master.working-area.edit', compact('workingArea', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkingAreaRequest $request, WorkingArea $workingArea)
    {
        $workingArea->update($request->validated());
        return redirect()->route('master.working-area.index')->with('success', 'Working area updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkingArea $workingArea)
    {
        $workingArea->delete();
        return redirect()->route('master.working-area.index')->with('success', 'Working area deleted successfully.');
    }
}
