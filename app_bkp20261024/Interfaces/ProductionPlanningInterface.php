<?php

namespace App\Interfaces;

use App\Models\LineRequirement;
use Illuminate\Support\Collection;

interface ProductionPlanningInterface
{
    public function getRequirementForDate(int $lineId, string $date);
    public function calculateLineStatus(int $lineId, string $date): Collection;
    public function getAllLineStatuses(string $date, $line_id = null): Collection;
    public function createOrUpdateRequirement(array $data): LineRequirement;
    public function getPlansForMonthPivoted(string $month): array;
    public function getRequirementForShift(int $lineId, string $date, string $shift_type): Collection;
    public function getLineDrawerDataForMonth(string $month): array;
    public function getAssignedWorkerIdsForDate(int $lineId, string $date): array;
    public function getShiftWiseWorkers($lineId, $date);
    public function getShiftWiseCountsForDate(string $date): array;
    public function getShiftWisePresentCountsForDate(string $date): array;
    public function getPresentCount($workerIds, $date);
}
