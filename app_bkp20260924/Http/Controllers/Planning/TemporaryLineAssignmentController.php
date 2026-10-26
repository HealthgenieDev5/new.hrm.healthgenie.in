<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Psp;
use App\Models\Shift;
use App\Models\Process;
use App\Services\TemporaryLineAssignmentDatatableService;
use Illuminate\Http\Request;

class TemporaryLineAssignmentController extends Controller
{
    public function index()
    {
        $psps = Psp::all();
        $shifts = Shift::all();
        $lines = Line::all();
        $processes = Process::all();
        $service = new TemporaryLineAssignmentDatatableService();
        $columns = $service->columns();
        $pageTitle = 'Temporary Line Assignments';

        return view('planning.temp-assignment.index', compact('psps', 'shifts', 'lines', 'processes', 'columns', 'pageTitle'));
    }

    public function getDatatable(Request $request)
    {
        $service = new TemporaryLineAssignmentDatatableService();
        return $service->getDatatable($request);
    }
}
