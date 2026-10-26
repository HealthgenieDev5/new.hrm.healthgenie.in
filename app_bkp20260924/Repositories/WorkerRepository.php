<?php

namespace App\Repositories;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WorkerRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getAllWorkers(Request $request): Builder
    {
        $workersQuery = Worker::query()->with([
            'psp:id,name',
            'shift:id,name',
            'designation:id,name',
            'division:id,name',
            'department:id,name',
            'subdepartment:id,name',
            'workingArea:id,name',
            'productionProduct:id,name',
            'line:id,name',
            'process:id,name',
            'level:id,name',
            'workertype:id,name',
        ]);
        if ($request->psp && $request->psp != 'all') {
            $workersQuery->where('psp_id', $request->psp);
        }
        if ($request->employment_status && $request->employment_status != 'all') {
            $workersQuery->where('employment_status', $request->employment_status);
        }
        return $workersQuery->latest();
    }

    public function createWorker(array $data): Worker
    {
        return Worker::create($data);
    }

    public function updateWorker(Worker $worker, array $data): void
    {
        $worker->update($data);
    }
}
