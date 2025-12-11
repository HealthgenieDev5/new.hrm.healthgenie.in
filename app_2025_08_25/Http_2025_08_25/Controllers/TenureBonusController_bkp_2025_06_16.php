<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\TenureBonusRule;
use App\Models\WorkerTenureBonus;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        // Get the selected month or default to current month
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($selectedMonth)->startOfMonth();
        $endDate = Carbon::parse($selectedMonth)->endOfMonth();

        $bonuses = WorkerTenureBonus::with(['worker', 'bonusRule'])
            ->whereBetween('bonus_date', [$startDate, $endDate])
            ->orderBy('bonus_date', 'desc')
            ->paginate(15);

        // Get summary statistics
        $summary = [
            'total_bonuses' => $bonuses->total(),
            'total_amount' => $bonuses->sum('bonus_amount'),
            'paid_bonuses' => $bonuses->where('is_paid', true)->count(),
            'paid_amount' => $bonuses->where('is_paid', true)->sum('bonus_amount'),
            'pending_bonuses' => $bonuses->where('is_paid', false)->count(),
            'pending_amount' => $bonuses->where('is_paid', false)->sum('bonus_amount'),
        ];

        return view('tenure-bonus.history', compact('bonuses', 'summary', 'selectedMonth', 'pageTitle'));
    }

    public function processBonuses()
    {
        //$workers = Worker::whereNull('date_of_leaving')->get();
        $workers = Worker::where('employment_status', 'Active')->get();
        $bonusRules = TenureBonusRule::where('is_active', true)->get();
        $processedCount = 0;

        foreach ($workers as $worker) {
            $joiningDate = Carbon::parse($worker->date_of_joining);
            $workingDays = $this->calculateWorkingDays($joiningDate, now());

            foreach ($bonusRules as $rule) {
                if ($workingDays >= $rule->working_days_start && $workingDays <= $rule->working_days_end) {
                    // Check if bonus was already given for this period
                    $existingBonus = WorkerTenureBonus::where('worker_id', $worker->id)
                        ->where('tenure_bonus_rule_id', $rule->id)
                        ->exists();

                    if (!$existingBonus) {
                        DB::transaction(function () use ($worker, $rule, $workingDays) {
                            WorkerTenureBonus::create([
                                'worker_id' => $worker->id,
                                'tenure_bonus_rule_id' => $rule->id,
                                'bonus_amount' => $rule->bonus_amount,
                                'bonus_date' => now(),
                                'working_days_completed' => $workingDays,
                                'is_completion_bonus' => $rule->is_completion_bonus,
                                'is_psp_worker' => $worker->is_psp_worker ?? false,
                                'remarks' => "Tenure bonus for completing {$rule->working_days_start} to {$rule->working_days_end} working days",
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

    private function calculateWorkingDays($startDate, $endDate)
    {
        $days = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $days++;
            }
            $currentDate->addDay();
        }

        return $days;
    }
}
