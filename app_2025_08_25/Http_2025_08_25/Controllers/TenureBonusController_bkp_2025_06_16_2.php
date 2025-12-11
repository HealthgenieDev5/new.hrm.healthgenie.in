<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\TenureBonusRule;
use App\Models\WorkerTenureBonus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class TenureBonusController extends Controller
{
    public function index()
    {
        $pageTitle = 'Tenure Bonuses';
        $bonusRules = TenureBonusRule::where('is_active', true)->get();
        $pendingBonuses = WorkerTenureBonus::with(['worker', 'bonusRule'])
            ->where('is_paid', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenure-bonus.index', compact('bonusRules', 'pendingBonuses', 'pageTitle'));
    }

    public function monthlyHistory(Request $request)
    {
        $pageTitle = 'Tenure Bonus History';
        $selectedMonthYear = $request->get('month', Carbon::now()->format('Y-m')); // Expects YYYY-MM
        $targetMonth = Carbon::parse($selectedMonthYear)->month;
        $targetYear = Carbon::parse($selectedMonthYear)->year;

        // Performance Note: Fetching all bonuses can be resource-intensive.
        // Consider optimizing if the table is very large, e.g., by adding an indexed 'actual_eligibility_date' column.
        $allBonuses = WorkerTenureBonus::with(['worker', 'bonusRule'])->get();

        $eligibleBonusesThisMonth = collect();

        foreach ($allBonuses as $bonus) {
            if (!$bonus->worker || !$bonus->bonusRule || !$bonus->worker->date_of_joining) {
                continue; // Skip if essential data is missing
            }

            $actualEligibilityDate = $this->calculateEligibilityActualDate(
                Carbon::parse($bonus->worker->date_of_joining),
                $bonus->bonusRule->working_days_start
            );

            if ($actualEligibilityDate->month == $targetMonth && $actualEligibilityDate->year == $targetYear) {
                // Store the calculated date for sorting
                $bonus->actual_eligibility_date_calculated = $actualEligibilityDate;
                $eligibleBonusesThisMonth->push($bonus);
            }
        }

        // Sort by the actual eligibility date, then by bonus ID as a tie-breaker
        $sortedBonuses = $eligibleBonusesThisMonth->sortBy(function ($bonus) {
            return $bonus->actual_eligibility_date_calculated->timestamp . '_' . $bonus->id;
        });

        // Manual pagination
        $perPage = 15;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageItems = $sortedBonuses->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedBonuses = new LengthAwarePaginator($currentPageItems, $sortedBonuses->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        // Calculate summary statistics from the full sorted set for the month
        $summary = [
            'total_bonuses' => $sortedBonuses->count(),
            'total_amount' => $sortedBonuses->sum('bonus_amount'),
            'paid_bonuses' => $sortedBonuses->where('is_paid', true)->count(),
            'paid_amount' => $sortedBonuses->where('is_paid', true)->sum('bonus_amount'),
            'pending_bonuses' => $sortedBonuses->where('is_paid', false)->count(),
            'pending_amount' => $sortedBonuses->where('is_paid', false)->sum('bonus_amount'),
        ];

        return view('tenure-bonus.history', compact('paginatedBonuses', 'summary', 'selectedMonthYear', 'pageTitle'));
    }

    public function processBonuses()
    {
        //$workers = Worker::whereNull('date_of_leaving')->get();
        $workers = Worker::where('employment_status', 'Active')->get();
        $bonusRules = TenureBonusRule::where('is_active', true)->get();
        $processedCount = 0;

        foreach ($workers as $worker) {
            $joiningDate = Carbon::parse($worker->date_of_joining);
            // $workingDays = $this->calculateWorkingDays($joiningDate, now()); // This calculates total working days until now

            foreach ($bonusRules as $rule) {
                // Calculate actual eligibility date for this specific rule milestone
                $actualEligibilityDateForRule = $this->calculateEligibilityActualDate($joiningDate, $rule->working_days_start);

                // Check if today is on or after the worker became eligible for this rule's start period
                // And if the bonus hasn't been given for this rule yet.
                // The original logic checked if current total working days fall within a rule's range.
                // We should ensure the bonus is given when the start_days milestone is hit.
                if (now()->greaterThanOrEqualTo($actualEligibilityDateForRule)) {
                    // Check if bonus was already given for this period
                    $existingBonus = WorkerTenureBonus::where('worker_id', $worker->id)
                        ->where('tenure_bonus_rule_id', $rule->id)
                        ->exists();

                    if (!$existingBonus) {
                        DB::transaction(function () use ($worker, $rule, $actualEligibilityDateForRule) {
                            WorkerTenureBonus::create([
                                'worker_id' => $worker->id,
                                'tenure_bonus_rule_id' => $rule->id,
                                'bonus_amount' => $rule->bonus_amount,
                                'bonus_date' => now(),
                                'working_days_completed' => $rule->working_days_start, // Use the rule's start days for this milestone
                                'is_completion_bonus' => $rule->is_completion_bonus,
                                'is_psp_worker' => $worker->is_psp_worker ?? false,
                                'actual_eligibility_date' => $actualEligibilityDateForRule, // If you add this column to DB
                                'remarks' => "Tenure bonus for rule starting at {$rule->working_days_start} working days.",
                            ]);
                        });
                        $processedCount++;
                    }
                }
            }
        }

        return redirect()->route('tenure-bonus.index')
            ->with('success', "Processed {$processedCount} new bonuses successfully!");
    }

    public function markAsPaid(Request $request, WorkerTenureBonus $bonus)
    {
        $bonus->update([
            'is_paid' => true,
            'paid_date' => now(),
            'payment_reference' => $request->payment_reference,
        ]);

        return redirect()->back()
            ->with('success', 'Bonus marked as paid successfully!');
    }

    /**
     * Calculates the actual date a worker becomes eligible after a certain number of working days.
     *
     * @param Carbon $startDate The worker's joining date.
     * @param int $numberOfWorkingDays The number of working days required by the rule.
     * @return Carbon The actual date of eligibility.
     */
    private function calculateEligibilityActualDate(Carbon $startDate, int $numberOfWorkingDays): Carbon
    {
        if ($numberOfWorkingDays <= 0) {
            // Assuming working_days_start is always >= 1 for a valid rule.
            // If 0 means on joining day, logic might need adjustment if joining day is weekend.
            return $startDate->copy();
        }

        $dateIterator = $startDate->copy();
        $daysFound = 0;
        while ($daysFound < $numberOfWorkingDays) {
            if ($dateIterator->isWeekday()) {
                $daysFound++;
            }
            if ($daysFound < $numberOfWorkingDays) {
               // $dateIterator->addDay();
                $dateIterator->addDay(); // Move to the next day
            }
        }
        return $dateIterator;
    }
}
