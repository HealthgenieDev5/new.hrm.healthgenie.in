<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Line;
use App\Http\Requests\StoreLineRequest;
use App\Http\Requests\UpdateLineRequest;
use App\Models\Process;
use Illuminate\Http\Request;

class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Manage Lines';

        $linesQuery = Line::query();
        $linesQuery->orderBy('name', 'ASC');
        if ($request->has('process_id')) {
            $linesQuery->whereHas('processes', function ($query) use ($request) {
                $query->where('process_id', $request->process_id);
            });
        }
        $lines = $linesQuery->paginate(10);

        return view('master.lines.index', compact('lines', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Line';
        $processes = Process::orderBy('name', 'ASC')->get()->all();
        return view('master.lines.create', compact('pageTitle', 'processes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:lines|max:255',
            'processes' => 'nullable|array',
            'processes.*' => 'exists:processes,id',
        ]);

        $line = Line::create($validated);

        if ($request->has('processes')) {
            $line->processes()->sync($request->processes);
        }

        return redirect()->route('master.lines.index')->with('success', 'Line created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Line $line)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Line $line)
    {
        $pageTitle = 'Edit Line';
        $processes = Process::orderBy('name', 'ASC')->get()->all();
        return view('master.lines.edit', compact('line', 'pageTitle', 'processes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Line $line)
    {
        $validated = $request->validate([
            'name' => 'required|unique:lines,name,' . $line->id . '|max:255',
            'processes' => 'nullable|array',
            'processes.*' => 'exists:processes,id',
        ]);

        $line->update($validated);

        if ($request->has('processes')) {
            $line->processes()->sync($request->processes);
        }

        return redirect()->route('master.lines.index')->with('success', 'Line updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Line $line)
    {
        $line->processes()->detach();
        $line->delete();

        return redirect()->route('master.lines.index')->with('success', 'Line deleted successfully.');
    }
}
