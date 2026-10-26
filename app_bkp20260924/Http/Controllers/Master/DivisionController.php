<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Divisions';
        $divisions = Division::orderBy('name', 'ASC')->paginate(10);
        return view('master.divisions.index', compact('divisions', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Division';
        $departments = Department::orderBy('name', 'ASC')->get()->all();
        return view('master.divisions.create', compact('pageTitle', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:divisions|max:255',
        ]);

        $division = Division::create($validated);

        if ($request->has('departments')) {
            $division->departments()->sync($request->departments);
        }

        return redirect()->route('master.divisions.index')->with('success', 'Division created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Division $division)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        $pageTitle = 'Edit Division';
        $departments = Department::orderBy('name', 'ASC')->get()->all();

        // dd($division->Workercount);
        return view('master.divisions.edit', compact('division', 'pageTitle', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|unique:divisions,name,' . $division->id . '|max:255',
        ]);

        $division->update($validated);

        if ($request->has('departments')) {
            $division->departments()->sync($request->departments);
        }

        return redirect()->route('master.divisions.index')->with('success', 'Division updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        $division->delete();

        return redirect()->route('master.divisions.index')->with('success', 'Division deleted successfully.');
    }
}
