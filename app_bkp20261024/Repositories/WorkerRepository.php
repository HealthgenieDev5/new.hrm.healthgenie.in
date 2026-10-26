<?php

namespace App\Repositories;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Collection;
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
    public function getAllWorkers(Request $request): Collection
    {
        $workersQuery = Worker::with(['psp', 'shift', 'workertype']);
        if ($request->psp && $request->psp != 'all') {
            $workersQuery->where('psp_id', $request->psp);
        }
        if ($request->employment_status && $request->employment_status != 'all') {
            $workersQuery->where('employment_status', $request->employment_status);
        }
        return $workersQuery->latest()->get();
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
