<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Workertype;
use App\Http\Requests\StoreWorkertypeRequest;
use App\Http\Requests\UpdateWorkertypeRequest;
use Illuminate\Http\Request;

class WorkertypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Worker Types';
        $workertypes = Workertype::paginate(10);
        return view('master.workertypes.index', compact('workertypes', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Worker Type';
        return view('master.workertypes.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:workertypes,name'],
            'minimum_wage' => ['required', 'numeric', 'min:0'],
        ]);

        Workertype::create($validated);

        return redirect()->route('master.workertypes.index')->with('success', 'Worker Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workertype $workertype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workertype $workertype)
    {
        $pageTitle = 'Edit Worker type';
        return view('master.workertypes.edit', compact('workertype', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workertype $workertype)
    {
        $validated = $request->validate([
            'name' => 'required|unique:workertypes,name,' . $workertype->id . '|max:255',
            'minimum_wage' => ['required', 'numeric', 'min:0'],
        ]);

        $workertype->update($validated);

        return redirect()->route('master.workertypes.index')->with('success', 'Worker Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workertype $workertype)
    {
        $workertype->delete();

        return redirect()->route('master.workertypes.index')->with('success', 'Worker Type deleted successfully.');
    }
}
