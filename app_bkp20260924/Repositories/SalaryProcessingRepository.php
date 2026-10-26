<?php

namespace App\Repositories;

use App\Models\ProcessedSalary;
use App\Models\ProcessedSalaryIncentive;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryProcessingRepository
{
    public function updateOrCreateSalary(array $conditions, array $data)
    {
        return ProcessedSalary::updateOrCreate($conditions, $data);
    }
    public function updateOrCreateIncentiveSalary(array $conditions, array $data)
    {
        return ProcessedSalaryIncentive::updateOrCreate($conditions, $data);
    }

    public function getProcessedSalaries(int $year, int $month, array $workerIds)
    {
        // Create Carbon instances for the first and last day of the given month.
        $firstOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $lastOfMonth  = Carbon::create($year, $month, 1)->endOfMonth();

        return ProcessedSalary::where('year', $year)
            ->where('month', $month)
            ->whereIn('worker_id', $workerIds)
            ->whereHas('worker', function ($query) use ($firstOfMonth, $lastOfMonth) {
                $query->where(function ($q) use ($firstOfMonth) {
                    // Worker is active if they haven't left or left on/after the first of the month
                    $q->whereNull('date_of_leaving')
                        ->orWhere('date_of_leaving', '>=', $firstOfMonth);
                })
                    ->where(function ($q) use ($lastOfMonth) {
                        // Worker must have joined on or before the last day of the month
                        $q->whereNull('date_of_joining')
                            ->orWhere('date_of_joining', '<=', $lastOfMonth);
                    });
            })
            ->get();
    }

    public function getProcessedIncentives(int $year, int $month, array $workerIds)
    {
        // Create Carbon instances for the first and last day of the given month.
        $firstOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $lastOfMonth  = Carbon::create($year, $month, 1)->endOfMonth();

        return ProcessedSalaryIncentive::where('year', $year)
            ->where('month', $month)
            ->where('paid_days', '>', 0)
            ->whereIn('worker_id', $workerIds)
            ->whereHas('worker', function ($query) use ($firstOfMonth, $lastOfMonth) {
                $query->where(function ($q) use ($firstOfMonth) {
                    // Worker is active if they haven't left or left on/after the first of the month
                    $q->whereNull('date_of_leaving')
                        ->orWhere('date_of_leaving', '>=', $firstOfMonth);
                })
                    ->where(function ($q) use ($lastOfMonth) {
                        // Worker must have joined on or before the last day of the month
                        $q->whereNull('date_of_joining')
                            ->orWhere('date_of_joining', '<=', $lastOfMonth);
                    });
            })
            ->get();
    }

    public function getFilteredWorkers(Request $request)
    {
        // $query = Worker::where('employment_status', 'Active');
        $query = Worker::orderBy('code', 'asc');

        if ($request->psp && $request->psp != 'all') {
            $query->where('psp_id', $request->psp);
        }

        if ($request->workers && !in_array('all', $request->workers)) {
            $query->where('id', $request->workers);
        }

        // $query->orderBy('code', 'asc');

        return $request->ajax()
            ? response()->json($query->select('id', 'name', 'code')->get())
            : $query->get();
    }
}
