<?php

namespace App\Services;

use App\Interfaces\ProductionPlanningInterface;
use App\Models\Line;
use App\Models\LineRequirement;
use App\Models\ProcessedAttendance;
use App\Models\Shift;
use App\Models\ShiftOverride;
use App\Models\TemporaryLineAssignment;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductionPlanningService implements ProductionPlanningInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function getAssignedWorkerIdsForDate(int $lineId, string $date): array
    {
        // Permanent active workers
        $permIds = Worker::where('line_id', $lineId)
            ->activeBetween($date, $date)
            ->pluck('id');

        // Temporary assigned workers
        $tempIds = \App\Models\TemporaryLineAssignment::where('line_id', $lineId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->pluck('worker_id');

        return $permIds->merge($tempIds)->unique()->values()->toArray();
    }

    public function getLineDrawerDataForMonth(string $month): array
    {
        $today = now()->toDateString();
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $startDate = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->startOfDay();
        $endDate = now()->isSameMonth($startDate)
            ? now()->endOfDay()
            : $startDate->copy()->endOfMonth();

        // Get all active workers for today once
        $allActiveWorkersToday = Worker::activeBetween($today, $today)->get();

        $allWorkerIds = $allActiveWorkersToday->pluck('id');
        $workerLineMap = $allActiveWorkersToday->pluck('line_id', 'id'); // [worker_id => line_id]

        // Get all temporary assignments active today
        $tempAssignmentsToday = \App\Models\TemporaryLineAssignment::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->groupBy('line_id');

        $data = [];

        foreach (Line::all() as $line) {
            $lineId = $line->id;

            // Permanent workers in this line
            $permIds = $workerLineMap->filter(fn($lineIdValue) => $lineIdValue === $lineId)->keys();

            // Temporary assigned to this line today
            $tempIds = $tempAssignmentsToday->get($lineId)?->pluck('worker_id') ?? collect();

            // Final assigned = permanent + temp
            $assignedIds = $permIds->merge($tempIds)->unique();

            // Assigned workers list
            $assignedWorkers = $allActiveWorkersToday->whereIn('id', $assignedIds);

            // Available workers = not in assigned
            $availableWorkers = $allActiveWorkersToday->whereNotIn('id', $assignedIds);

            $data[$lineId] = [
                'assigned' => $assignedWorkers->values(),
                'available' => $availableWorkers->values(),
            ];
        }

        return $data;
    }

    public function getRequirementForDate(int $lineId, string $date)
    {
        return LineRequirement::where('line_id', $lineId)
            ->whereDate('date', '<=', $date)
            ->orderByDesc('date')
            ->first();
    }

    public function calculateLineStatus(int $lineId, string $date): Collection
    {
        $line = Line::findOrFail($lineId);

        $requirement = $this->getRequirementForDate($lineId, $date);

        if (!$requirement) {
            return collect(
                [
                    'line_id' => $line->id,
                    'line_name' => $line->name,
                    'message' => 'No requirement found',
                    'status' => 'incomplete',
                ]
            );
        }

        $assignedWorkerIds = $this->getAssignedWorkerIdsForDate($line->id, $date);

        $presentCount = ProcessedAttendance::whereDate('date', '=', $date)
            ->whereIn('status', ['P', 'M/P', 'H/D', 'HD']) // Adjust according to your "present" codes
            ->whereIn('worker_id', $assignedWorkerIds)
            ->count();

        $difference = $requirement->workers - $presentCount;
        $shortfall = max(0, $difference);
        $excess = max(0, -$difference);

        return collect(
            [
                'line_id' => $line->id,
                'line_name' => $line->name,
                'workers' => $requirement->workers,
                'extras' => $requirement->extras,
                'present' => $presentCount,
                'shortfall' => $shortfall,
                'excess' => $excess,
                'difference' => $difference, // Negative = excess, Positive = shortfall
                'supervisors' => $requirement->supervisors,
                'qas' => $requirement->qas,
                'services' => $requirement->services,
                'movers' => $requirement->movers,
                'status' => $difference > 0 ? 'short' : ($difference < 0 ? 'excess' : 'ok'),
            ]
        );
    }

    public function getAllLineStatuses(string $date, $line_id = null): Collection
    {
        if (!empty($line_id)) {
            $lines = collect([Line::findOrFail($line_id)]);
        } else {
            $lines = Line::all();
        }

        $report =  $lines->map(function ($line) use ($date) {
            return $this->calculateLineStatus($line->id, $date);
        });
        return $report;
    }

    public function createOrUpdateRequirement(array $data): LineRequirement
    {
        return LineRequirement::updateOrCreate(
            [
                'line_id' => $data['line_id'],
                'date' => $data['date'],
                'shift_type' => $data['shift_type'],
            ],
            [
                'workers' => $data['workers'],
                'extras' => $data['extras'] ?? 0,
                'supervisors' => $data['supervisors'] ?? 0,
                'qas' => $data['qas'] ?? 0,
                'services' => $data['services'] ?? 0,
                'movers' => $data['movers'] ?? 0,
            ]
        );
    }

    //test
    public function getPlansForMonthPivoted(string $month): array
    {
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $lastDay = now()->isSameMonth(Carbon::createFromDate($year, $monthNum, 1)) ? now()->day : Carbon::createFromDate($year, $monthNum)->daysInMonth;
        $datesInMonth = collect(range(1, $lastDay))->map(fn($day) => Carbon::createFromDate($year, $monthNum, $day)->toDateString());

        $lines = Line::pluck('name', 'id');

        $allRecords = LineRequirement::with('line')
            ->where('date', '<=', $datesInMonth->last())
            ->orderBy('date')
            ->get()
            ->groupBy('line_id');

        // $presentCounts = $this->getPresentCountsByLineAndDate($month);
        // Further group by shift type for quick lookup
        $allRecords = $allRecords->map(function ($records) {
            return $records->groupBy('shift_type');
        });

        // Precompute assigned and present counts for each date
        $assignedPerDate = [];
        $presentPerDate = [];

        foreach ($datesInMonth as $date) {
            $assignedPerDate[$date] = $this->getShiftWiseCountsForDate($date);
            $presentPerDate[$date] = $this->getShiftWisePresentCountsForDate($date);
        }

        $result = [];

        foreach ($lines as $lineId => $lineName) {
            $lineRecords = $allRecords->get($lineId, collect());
            $dayRecords = $lineRecords->get('day', collect());
            $nightRecords = $lineRecords->get('night', collect());

            // $latestPlan = null;
            $latestDay = null;
            $latestNight = null;

            foreach ($datesInMonth as $date) {

                $todayDay = $dayRecords->firstWhere('date', $date);
                $todayNight = $nightRecords->firstWhere('date', $date);

                $latestDay = $todayDay ? $todayDay : ($latestDay ?? $dayRecords->where('date', '<', $date)->last());
                $latestNight = $todayNight ? $todayNight : ($latestNight ?? $nightRecords->where('date', '<', $date)->last());

                $result[$lineName]['line_id'] = $lineId;
                $result[$lineName]['total'][$date] = [
                    'day' => 0,
                    'night' => 0,
                ];
                $result[$lineName]['workers'][$date] = [
                    'day' => $latestDay->workers ?? 0,
                    'night' => $latestNight->workers ?? 0,
                ];
                $result[$lineName]['extras'][$date] = [
                    'day' => $latestDay->extras ?? 0,
                    'night' => $latestNight->extras ?? 0,
                ];
                $result[$lineName]['supervisors'][$date] = [
                    'day' => $latestDay->supervisors ?? 0,
                    'night' => $latestNight->supervisors ?? 0,
                ];
                $result[$lineName]['qas'][$date] = [
                    'day' => $latestDay->qas ?? 0,
                    'night' => $latestNight->qas ?? 0,
                ];
                $result[$lineName]['services'][$date] = [
                    'day' => $latestDay->services ?? 0,
                    'night' => $latestNight->services ?? 0,
                ];
                $result[$lineName]['movers'][$date] = [
                    'day' => $latestDay->movers ?? 0,
                    'night' => $latestNight->movers ?? 0,
                ];

                $result[$lineName]['total'][$date] = [
                    'day' => $result[$lineName]['workers'][$date]['day'] +
                        $result[$lineName]['extras'][$date]['day'] +
                        $result[$lineName]['supervisors'][$date]['day'] +
                        $result[$lineName]['qas'][$date]['day'] +
                        $result[$lineName]['services'][$date]['day'] +
                        $result[$lineName]['movers'][$date]['day'],

                    'night' => $result[$lineName]['workers'][$date]['night'] +
                        $result[$lineName]['extras'][$date]['night'] +
                        $result[$lineName]['supervisors'][$date]['night'] +
                        $result[$lineName]['qas'][$date]['night'] +
                        $result[$lineName]['services'][$date]['night'] +
                        $result[$lineName]['movers'][$date]['night'],
                ];

                $assigned = $assignedPerDate[$date][$lineId] ?? ['day' => 0, 'night' => 0];
                $present = $presentPerDate[$date][$lineId] ?? ['day' => 0, 'night' => 0];

                $result[$lineName]['assigned'][$date] = $assigned;

                $result[$lineName]['present'][$date] = $present;

                $dayDifference = $result[$lineName]['total'][$date]['day'] - $present['day'];
                $nightDifference = $result[$lineName]['total'][$date]['night'] - $present['night'];

                $result[$lineName]['shortfall'][$date] = [
                    'day' => max(0, $dayDifference),
                    'night' => max(0, $nightDifference),
                ];
                $result[$lineName]['excess'][$date] = [
                    'day' => max(0, -$dayDifference),
                    'night' => max(0, -$nightDifference),
                ];
                $result[$lineName]['difference'][$date] = [
                    'day' => $dayDifference,
                    'night' => $nightDifference,
                ];
                $result[$lineName]['status'][$date] = [
                    'day' => $dayDifference > 0 ? 'short' : ($dayDifference < 0 ? 'excess' : 'ok'),
                    'night' => $nightDifference > 0 ? 'short' : ($nightDifference < 0 ? 'excess' : 'ok'),
                ];
            }
        }

        return [
            'data' => collect($result),
            'dates' => $datesInMonth,
        ];
    }


    public function getRequirementForShift(int $lineId, string $date, string $shift_type): Collection
    {
        $requirement = LineRequirement::where('line_id', $lineId)
            ->where('shift_type', $shift_type)
            ->whereDate('date', '<=', $date)
            ->orderByDesc('date')
            ->first();

        return collect([
            'workers' => $requirement?->workers ?? 0,
            'extras' => $requirement?->extras ?? 0,
            'supervisors' => $requirement?->supervisors ?? 0,
            'qas' => $requirement?->qas ?? 0,
            'services' => $requirement?->services ?? 0,
            'movers' => $requirement?->movers ?? 0,
        ]);
    }


    public function getShiftWiseWorkers($lineId, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $permWorkers = Worker::where('line_id', $lineId)
            ->activeBetween($date, $date)
            ->get();

        // Get workers temporarily assigned to other lines on the same date
        $tempAssignedElsewhere = TemporaryLineAssignment::where('line_id', '!=', $lineId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->pluck('worker_id');

        // Filter out these from permWorkers
        $permWorkers = $permWorkers->filter(fn($worker) => !$tempAssignedElsewhere->contains($worker->id));

        $tempWorkers = TemporaryLineAssignment::with('worker')
            ->where('line_id', $lineId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get()
            ->pluck('worker');

        $allWorkers = $permWorkers->merge($tempWorkers)->unique('id')->values();

        $availableWorkers = Worker::whereNotIn('id', $allWorkers->pluck('id'))
            ->activeBetween($date, $date)
            ->get();

        // Load shift overrides and shift data
        $overrides = ShiftOverride::whereIn('worker_id', $allWorkers->pluck('id'))
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get()
            ->keyBy('worker_id');

        $shifts = Shift::all()->keyBy('id');
        // dd($shifts);

        $dayWorkers = collect();
        $nightWorkers = collect();
        $otherWorkers = collect();

        foreach ($allWorkers as $worker) {
            $override = $overrides->get($worker->id);
            $shiftId = $override ? $override->shift_id : $worker->shift_id;
            $shiftType = $shifts->get($shiftId)?->shift_type;

            if ($shiftType === 'day') {
                $dayWorkers->push($worker);
            } elseif ($shiftType === 'night') {
                $nightWorkers->push($worker);
            }
        }

        $combined = [
            'day' => $dayWorkers,
            'night' => $nightWorkers,
            'availableWorkers' => $availableWorkers,
        ];

        return $combined;
    }

    public function getShiftWiseCountsForDate(string $date): array
    {
        $counts = [];

        foreach (Line::all() as $line) {
            $workers = $this->getShiftWiseWorkers($line->id, $date);
            $counts[$line->id] = [
                'day' => $workers['day']->count(),
                'night' => $workers['night']->count(),
            ];
        }

        return $counts;
    }

    public function getShiftWisePresentCountsForDate(string $date): array
    {
        $present = [];

        foreach (Line::all() as $line) {
            $workers = $this->getShiftWiseWorkers($line->id, $date);
            $present[$line->id] = [
                'day' => $this->getPresentCount($workers['day']->pluck('id'), $date),
                'night' => $this->getPresentCount($workers['night']->pluck('id'), $date),
            ];
        }

        return $present;
    }

    public function getPresentCount($workerIds, $date)
    {
        return ProcessedAttendance::whereIn('worker_id', $workerIds)
            ->whereDate('date', $date)
            ->whereIn('status', ['P', 'M/P']) // Include more if needed
            ->count();
    }
}
