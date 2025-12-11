<?php

namespace App\Interfaces;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface WorkerAttendanceRepositoryInterface
{
    public function getMonthlyAttendanceSummaryWorkers(Request $request): Collection;

    public function getMonthlyAttendanceSummaryProcessedAttendance(Carbon $startDate, Carbon $endDate): Collection;

    public function getTimeAttendanceWorkers(Request $request);

    public function getContinuousAbsentWorkerIds(Carbon $from, Carbon $to, Request $request);
    public function getContinuousAbsentAttendanceSummary($startOfLM, $endOfLM, $startOfMTD, $endOfMTD, $absentWorkerIds);
    public function getContinuousAbsentWorkerWithDetail($absentWorkerIds);
}
