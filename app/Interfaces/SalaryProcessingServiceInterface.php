<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface SalaryProcessingServiceInterface
{
    public function processSalaries(array $workerCodes, int $year, int $month): void;

    public function getProcessedSalaries(int $year, int $month, array $workerIds);
    public function getProcessedIncentives(int $year, int $month, array $workerIds);
    public function getFilteredWorkers(Request $request);
}
