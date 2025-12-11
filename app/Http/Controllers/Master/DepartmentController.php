<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Division;
use App\Models\Department;
use App\Models\Subdepartment;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Manage Departments';

        $departmentsQuery = Department::query();
        $departmentsQuery->orderBy('name', 'ASC');
        if ($request->has('division_id') && $request->division_id != 'all') {
            $departmentsQuery->whereHas('divisions', function ($query) use ($request) {
                $query->where('division_id', $request->division_id);
            });
        }
        $departments = $departmentsQuery->paginate(10);

        $divisions = Division::all();

        return view('master.departments.index', compact('departments', 'pageTitle', 'divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Department';
        $subdepartments = Subdepartment::orderBy('name', 'ASC')->get()->all();
        return view('master.departments.create', compact('pageTitle', 'subdepartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:departments|max:255',
            'description' => 'nullable|max:500',
            'subdepartments' => 'nullable|array',
            'subdepartments.*' => 'exists:subdepartments,id',
        ]);

        $department = Department::create($validated);

        if ($request->has('subdepartments')) {
            $department->subdepartments()->sync($request->subdepartments);
        }

        return redirect()->route('master.departments.index')->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $pageTitle = 'Edit Department';
        $subdepartments = Subdepartment::orderBy('name', 'ASC')->get()->all();
        return view('master.departments.edit', compact('department', 'pageTitle', 'subdepartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|unique:departments,name,' . $department->id . '|max:255',
            'description' => 'nullable|max:500',
            'subdepartments' => 'nullable|array',
            'subdepartments.*' => 'exists:subdepartments,id',
        ]);

        $department->update($validated);

        if ($request->has('subdepartments')) {
            $department->subdepartments()->sync($request->subdepartments);
        }

        return redirect()->route('master.departments.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->subdepartments()->detach();
        $department->delete();

        return redirect()->route('master.departments.index')->with('success', 'Department deleted successfully.');
    }
}
