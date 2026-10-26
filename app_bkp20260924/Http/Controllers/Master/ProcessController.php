<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Process;
use App\Http\Requests\StoreProcessRequest;
use App\Http\Requests\UpdateProcessRequest;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Processs';
        $processes = Process::orderBy('name', 'ASC')->paginate(10);
        return view('master.processes.index', compact('processes', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Process';
        return view('master.processes.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:processes|max:255',
        ]);

        Process::create($validated);

        return redirect()->route('master.processes.index')->with('success', 'Process created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Process $process)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Process $process)
    {
        $pageTitle = 'Edit Process';
        return view('master.processes.edit', compact('process', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Process $process)
    {
        $validated = $request->validate([
            'name' => 'required|unique:processes,name,' . $process->id . '|max:255',
        ]);

        $process->update($validated);

        return redirect()->route('master.processes.index')->with('success', 'Process updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Process $process)
    {
        $process->delete();

        return redirect()->route('master.processes.index')->with('success', 'Process deleted successfully.');
    }
}
