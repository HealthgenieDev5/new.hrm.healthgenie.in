<?php

namespace App\Services;

use App\Interfaces\SalaryProcessingServiceInterface;
use App\Models\Worker;
use App\Repositories\SalaryProcessingRepository;
use App\Services\ServiceHelpers\WorkerAttendanceServiceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalaryProcessingService implements SalaryProcessingServiceInterface
{
    protected $salaryProcessingRepository;
    private $workerAttendanceServiceHelper;

    public function __construct(SalaryProcessingRepository $salaryProcessingRepository, WorkerAttendanceServiceHelper $workerAttendanceServiceHelper)
    {
        $this->salaryProcessingRepository = $salaryProcessingRepository;
        $this->workerAttendanceServiceHelper = $workerAttendanceServiceHelper;
    }

    public function processSalaries(array $workerCodes, int $year, int $month): void
    {
        $firstOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $lastOfMonth  = Carbon::create($year, $month, 1)->endOfMonth();

        $workers = Worker::with(
            [
                'processedAttendance' => fn($query) => $query
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month),
                'salaryStructure'
            ]
        )
            ->when($workerCodes, fn($query) => $query->whereIn('code', $workerCodes))
            // ->where('employment_status', 'Active')
            ->where(function ($query) use ($firstOfMonth) {
                $query->whereNull('date_of_leaving')
                    ->orWhere('date_of_leaving', '>=', $firstOfMonth);
            })
            ->where(function ($query) use ($lastOfMonth) {
                $query->whereNull('date_of_joining')
                    ->orWhere('date_of_joining', '<=', $lastOfMonth);
            })

            ->get(['id', 'code', 'name']);

        // dd($workers->toArray());

        if (!empty($workers)) {
            foreach ($workers as $worker) {
                $this->processSalary($worker, $year, $month);
                $this->processIncentiveSalary($worker, $year, $month);
            }
        }
    }

    public function processSalary(Worker $worker, int $year, int $month)
    {
        if (!$worker->salaryStructure) {
            Log::info("Salary structure is not available of " . $worker->name . " (" . $worker->code . ")");
            return;
        }
        if (!$worker->processedAttendance) {
            Log::info("Attendance is not available of " . $worker->name . " (" . $worker->code . ")");
            return;
        }

        $attendance = $worker->processedAttendance;
        $totalDays = Carbon::createFromDate($year, $month)->daysInMonth;

        $presentWithWeekOff = $this->workerAttendanceServiceHelper->getPresentCount($attendance ? $attendance->pluck('status')->toArray() : []);
        $halfDayWithWeekOff = $this->workerAttendanceServiceHelper->getHalfDayCount($attendance ? $attendance->pluck('status')->toArray() : []);
        $absentWithWeekOffAbsent = $this->workerAttendanceServiceHelper->getAbsentCount($attendance ? $attendance->pluck('status')->toArray() : []);
        $presentOnWeekOff = $this->workerAttendanceServiceHelper->countPresentOnWeekOff($attendance);
        $halfDayOnWeekOff = $this->workerAttendanceServiceHelper->countHalfDayOnWeekOff($attendance);
        $newTotalPresentDays = $presentWithWeekOff - $presentOnWeekOff; #Ok
        $newTotalHalfDays = $halfDayWithWeekOff - $halfDayOnWeekOff; #Ok
        $newTotalAbsent = $absentWithWeekOffAbsent - ($halfDayOnWeekOff / 2);
        $sandwichOnWeekOff = $this->workerAttendanceServiceHelper->countSandwichOnWeekOff($attendance);
        $totalIsWeekOff = $this->workerAttendanceServiceHelper->countTotalIsWeekOff($attendance);
        $newTotalWeekOff = $totalIsWeekOff - $sandwichOnWeekOff; #OK
        $totalHolidayAndRh = $this->workerAttendanceServiceHelper->getHolidayAndRhCount($attendance ? $attendance->pluck('status')->toArray() : []);
        $newTotalPaidDays = $newTotalPresentDays + ($newTotalHalfDays / 2) + $newTotalWeekOff + $totalHolidayAndRh;
        $newDeductionDays = $totalDays - $newTotalPaidDays;

        // dd($newDeductionDays);


        // $weekOffs = $attendance->where('status', 'W/O')->count();
        // $els = $attendance->where('status', 'EL')->count();
        // $cls = $attendance->where('status', 'CL')->count();
        // $holidays = $attendance->where('status', 'HL')->count();
        // $rhs = $attendance->where('status', 'RH')->count();

        // $presentOnWeekOff = $attendance->where('status', 'P')->where('is_weekoff', 1)->count();
        // $presentOnWeekOff = $attendance->filter(fn($item) => $item->status === 'P' && (bool) $item->is_weekoff)->count();
        // $halfDayOnWeekOff = $attendance->filter(fn($item) => $item->status === 'H/D' && (bool) $item->is_weekoff)->count();

        // $paidDays = $presentDays + $halfDays + $weekOffs + $els + $cls + $holidays + $rhs;
        $paidDays = $newTotalPaidDays;



        #Salary Components
        $salaryStructure = $worker->salaryStructure;
        $basic = $salaryStructure->basic * ($paidDays / $totalDays);
        $hra = $salaryStructure->hra * ($paidDays / $totalDays);
        $conveyance = $salaryStructure->conveyance * ($paidDays / $totalDays);
        $medical_allowance = $salaryStructure->medical_allowance * ($paidDays / $totalDays);
        $special_allowance = $salaryStructure->special_allowance * ($paidDays / $totalDays);
        $fuel_allowance = $salaryStructure->fuel_allowance * ($paidDays / $totalDays);
        $vacation_allowance = $salaryStructure->vacation_allowance * ($paidDays / $totalDays);
        $other_allowance = $salaryStructure->other_allowance * ($paidDays / $totalDays);
        // $other_allowance = $presentOnWeekOff;

        #Gross Salary
        $gross = $basic + $hra + $conveyance + $medical_allowance + $special_allowance + $fuel_allowance + $vacation_allowance + $other_allowance;

        #Deductions (PF, ESI)
        $pf_employee = $salaryStructure->pf_allowed ? min(15000, $basic) * 0.12 : 0;
        $pf_employer = $salaryStructure->pf_allowed ? min(15000, $basic) * 0.13 : 0;
        // $esic_employee = $salaryStructure->esic_allowed ? $gross * 0.0075 : 0;
        // $esic_employer = $salaryStructure->esic_allowed ? $gross * 0.0325 : 0;
        // Updated on 06 May asked by Santu sir based on ESIC rules that if the basic is more than 21000 then no ESIC will be deducted and From April 2026, the esic will be deducted on basic salary instead of gross salary and the maximum basic salary limit for ESIC deduction will be 21000. So, I have updated the code as per the new rules.
        $esic_employee = $salaryStructure->esic_allowed ? min(21000, $basic) * 0.0075 : 0;
        $esic_employer = $salaryStructure->esic_allowed ? min(21000, $basic) * 0.0325 : 0;

        #Bonus Calculation
        $bonus = $salaryStructure->bonus_allowed ? ($worker?->workertype?->minimum_wage ?? 0) * 0.0833 : 0;

        #Net Salary
        $netSalary = $gross - ($pf_employee + $esic_employee);

        #Create or update salary record
        $this->salaryProcessingRepository->updateOrCreateSalary([
            'worker_id' => $worker->id,
            'year' => $year,
            'month' => $month,
        ], [
            'basic' => $basic,
            'hra' => $hra,
            'conveyance' => $conveyance,
            'medical_allowance' => $medical_allowance,
            'special_allowance' => $special_allowance,
            'fuel_allowance' => $fuel_allowance,
            'vacation_allowance' => $vacation_allowance,
            'other_allowance' => $other_allowance,
            'gross' => $gross,
            'pf_employee' => $pf_employee,
            'pf_employer' => $pf_employer,
            'esic_employee' => $esic_employee,
            'esic_employer' => $esic_employer,
            'bonus' => $bonus,
            'net_salary' => $netSalary,
            'salary_structure' => $worker->salaryStructure,
            'paid_days' => $paidDays
        ]);
    }

    public function getProcessedSalaries(int $year, int $month, array $workerIds)
    {
        return $this->salaryProcessingRepository->getProcessedSalaries($year, $month, $workerIds);
    }

    public function getFilteredWorkers(Request $request)
    {
        return $this->salaryProcessingRepository->getFilteredWorkers($request);
    }

    public function processIncentiveSalary(Worker $worker, int $year, int $month)
    {
        if (!$worker->salaryStructure) {
            Log::info("Salary structure is not available of " . $worker->name . " (" . $worker->code . ")");
            return;
        }
        if (!$worker->processedAttendance) {
            Log::info("Attendance is not available of " . $worker->name . " (" . $worker->code . ")");
            return;
        }

        $attendance = $worker->processedAttendance;
        $totalDays = Carbon::createFromDate($year, $month)->daysInMonth;
        $presentOnWeekOff = $this->workerAttendanceServiceHelper->countPresentOnWeekOff($attendance);
        $halfDayOnWeekOff = $this->workerAttendanceServiceHelper->countHalfDayOnWeekOff($attendance);
        $totalIncentiveWeekOff = $presentOnWeekOff + ($halfDayOnWeekOff / 2);

        #Salary Components
        $salaryStructure = $worker->salaryStructure;
        $basic = $salaryStructure->basic * ($totalIncentiveWeekOff / $totalDays);
        $hra = $salaryStructure->hra * ($totalIncentiveWeekOff / $totalDays);
        $conveyance = $salaryStructure->conveyance * ($totalIncentiveWeekOff / $totalDays);
        $medical_allowance = $salaryStructure->medical_allowance * ($totalIncentiveWeekOff / $totalDays);
        $special_allowance = $salaryStructure->special_allowance * ($totalIncentiveWeekOff / $totalDays);
        $fuel_allowance = $salaryStructure->fuel_allowance * ($totalIncentiveWeekOff / $totalDays);
        $vacation_allowance = $salaryStructure->vacation_allowance * ($totalIncentiveWeekOff / $totalDays);
        $other_allowance = $salaryStructure->other_allowance * ($totalIncentiveWeekOff / $totalDays);
        // $other_allowance = $presentOnWeekOff;

        #Gross Salary
        $gross = $basic + $hra + $conveyance + $medical_allowance + $special_allowance + $fuel_allowance + $vacation_allowance + $other_allowance;

        #Deductions (PF, ESI)
        $pf_employee = 0;
        $pf_employer = 0;
        $esic_employee = 0;
        $esic_employer = 0;

        #Bonus Calculation
        $bonus = 0;

        #Net Salary
        $netSalary = $gross - ($pf_employee + $esic_employee);

        #Create or update salary record
        $this->salaryProcessingRepository->updateOrCreateIncentiveSalary([
            'worker_id' => $worker->id,
            'year' => $year,
            'month' => $month,
        ], [
            'basic' => $basic,
            'hra' => $hra,
            'conveyance' => $conveyance,
            'medical_allowance' => $medical_allowance,
            'special_allowance' => $special_allowance,
            'fuel_allowance' => $fuel_allowance,
            'vacation_allowance' => $vacation_allowance,
            'other_allowance' => $other_allowance,
            'gross' => $gross,
            'pf_employee' => $pf_employee,
            'pf_employer' => $pf_employer,
            'esic_employee' => $esic_employee,
            'esic_employer' => $esic_employer,
            'bonus' => $bonus,
            'net_salary' => $netSalary,
            'salary_structure' => $worker->salaryStructure,
            'paid_days' => $totalIncentiveWeekOff
        ]);
    }

    public function getProcessedIncentives(int $year, int $month, array $workerIds)
    {
        return $this->salaryProcessingRepository->getProcessedIncentives($year, $month, $workerIds);
    }
}
