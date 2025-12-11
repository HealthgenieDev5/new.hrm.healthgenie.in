<?php

namespace App\Console\Commands;

use App\Models\Worker;
use App\Models\TenureBonusRule;
use App\Models\WorkerTenureBonus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\TenureBonusService;
use Illuminate\Support\Facades\Log;

class ProcessTenureBonuses extends Command
{
    // protected $signature = 'workers:process-tenure-bonuses';
    protected $signature = 'bonus:process';
    protected $description = 'Process tenure-based bonuses for eligible workers';

    public function __construct(private TenureBonusService $tenureBonusService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Processing tenure bonuses...');

        // $workers = Worker::whereNull('date_of_leaving')->get();
        $workers = Worker::where('employment_status', 'Active')->whereNull('date_of_leaving')->get();

        $bonusRules = TenureBonusRule::where('is_active', true)
            ->orderBy('working_days_start')
            ->get();


        foreach ($workers as $worker) {

            foreach ($bonusRules as $rule) {

                $actualEligibilityDateForRule = $this->tenureBonusService->calculateEligibilityActualDate($worker, $rule->working_days_start);

                if (!$actualEligibilityDateForRule || now()->lt($actualEligibilityDateForRule)) {
                    $this->warn("Worker {$worker->id} - {$worker->name} is not yet eligible for rule ID {$rule->id}");
                    continue; // Skip this rule for this worker
                }

                if (now()->greaterThanOrEqualTo($actualEligibilityDateForRule)) {
                    $existingBonus = WorkerTenureBonus::where('worker_id', $worker->id)
                        ->where('tenure_bonus_rule_id', $rule->id)
                        ->exists();

                    if (!$existingBonus) {

                        $this->info("Found eligible bonus. Rule ID: {$rule->id}, Bonus Amount: {$rule->bonus_amount}");
                        DB::transaction(function () use ($worker, $rule, $actualEligibilityDateForRule) {
                            WorkerTenureBonus::create([
                                'worker_id' => $worker->id,
                                'tenure_bonus_rule_id' => $rule->id,
                                'bonus_amount' => $rule->bonus_amount,
                                'bonus_date' => now(),
                                'working_days_completed' => $rule->working_days_start,
                                'is_completion_bonus' => $rule->is_completion_bonus,
                               // 'is_psp_worker' => $worker->psp_id ?? false,
                               'is_psp_worker' =>$worker->psp_id != 11,
                                'actual_eligibility_date' => $actualEligibilityDateForRule,
                                'remarks' => "Tenure bonus for rule starting at {$rule->working_days_start} working days.",
                            ]);
                        });

                        $this->info("Created bonus for worker {$worker->code} - {$worker->name} for {$rule->working_days_start} working days milestone");
                    }
                }

            }
        }

        $this->info('Tenure bonus processing completed!');
    }
}
