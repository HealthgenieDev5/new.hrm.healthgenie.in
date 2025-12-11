<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Line;
use App\Models\Subdepartment;
use Illuminate\Http\Request;

class SubdepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Manage Subdepartments';
        $subdepartmentsQuery = Subdepartment::query();
        $subdepartmentsQuery->orderBy('name', 'ASC');

        if ($request->has('line_id')) {
            $subdepartmentsQuery->whereHas('lines', function ($query) use ($request) {
                $query->where('line_id', $request->line_id);
            });
        }
        $subdepartments = $subdepartmentsQuery->paginate(100);


        return view('master.subdepartments.index', compact('subdepartments', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Subdepartment';
        $lines = Line::orderBy('name', 'ASC')->get()->all();
        return view('master.subdepartments.create', compact('pageTitle', 'lines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subdepartments|max:255',
            'description' => 'nullable|max:500',
            'lines' => 'nullable|array',
            'lines.*' => 'exists:lines,id',
        ]);

        $subdepartment = Subdepartment::create($validated);

        if ($request->has('lines')) {
            $subdepartment->lines()->sync($request->lines);
        }

        return redirect()->route('master.subdepartments.index')->with('success', 'Subdepartment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subdepartment $subdepartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subdepartment $subdepartment)
    {
        $pageTitle = 'Edit Subdepartment';
        $lines = Line::orderBy('name', 'ASC')->get()->all();
        return view('master.subdepartments.edit', compact('subdepartment', 'pageTitle', 'lines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subdepartment $subdepartment)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subdepartments,name,' . $subdepartment->id . '|max:255',
            'description' => 'nullable|max:500',
            'lines' => 'nullable|array',
            'lines.*' => 'exists:lines,id',
        ]);

        $subdepartment->update($validated);

        if ($request->has('lines')) {
            $subdepartment->lines()->sync($request->lines);
        }

        return redirect()->route('master.subdepartments.index')->with('success', 'Subdepartment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subdepartment $subdepartment)
    {
        $subdepartment->lines()->detach();
        $subdepartment->delete();

        return redirect()->route('master.subdepartments.index')->with('success', 'Subdepartment deleted successfully.');
    }
}
