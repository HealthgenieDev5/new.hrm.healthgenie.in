<?php

namespace App\Services;

use App\Models\TemporaryLineAssignment;
use Illuminate\Http\Request;

class TemporaryLineAssignmentDatatableService
{
    public function getDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getData($request);
            return datatables()->of($data)->make(true);
        }
    }

    public function columns(): array
    {
        return [
            ['data' => 'code', 'name' => 'Worker Code', 'searchable' => true],
            ['data' => 'name', 'name' => 'Worker Name', 'searchable' => true],
            ['data' => 'count', 'name' => 'Total Assignments'],
            ['data' => 'line', 'name' => 'Line'],
            ['data' => 'shift', 'name' => 'Shift'],
            ['data' => 'start_date', 'name' => 'From'],
            ['data' => 'end_date', 'name' => 'To'],
        ];
    }

    private function getData(Request $request)
    {
        $assignments = TemporaryLineAssignment::with(['worker.psp', 'line', 'shift'])
            ->when($request->filter_psp && $request->filter_psp !== 'all', function ($q) use ($request) {
                $q->whereHas('worker', function ($qq) use ($request) {
                    $qq->where('psp_id', $request->filter_psp);
                });
            })
            ->when($request->filter_line && $request->filter_line !== 'all', function ($q) use ($request) {
                $q->where('line_id', $request->filter_line);
            })
            ->when($request->filter_shift && $request->filter_shift !== 'all', function ($q) use ($request) {
                $q->where('shift_id', $request->filter_shift);
            })
            ->when($request->filter_from, function ($q) use ($request) {
                $q->whereDate('start_date', '>=', $request->filter_from);
            })
            ->when($request->filter_to, function ($q) use ($request) {
                $q->whereDate('end_date', '<=', $request->filter_to);
            })
            ->orderBy('worker_id')
            ->orderBy('start_date')
            ->get()
            ->groupBy('worker_id');

        $data = collect();
        foreach ($assignments as $workerId => $list) {
            $worker = $list->first()->worker;
            $count = $list->count();
            foreach ($list as $index => $assignment) {
                $data->push([
                    'code' => $index === 0 ? $worker->code : '',
                    'name' => $index === 0 ? $worker->name : '',
                    'count' => $index === 0 ? $count : '',
                    'line' => $assignment->line->name,
                    'shift' => $assignment->shift->name,
                    'start_date' => $assignment->start_date->format('d M Y'),
                    'end_date' => $assignment->end_date->format('d M Y'),
                ]);
            }
        }

        return $data;
    }
}
