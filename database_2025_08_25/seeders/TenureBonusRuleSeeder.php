<?php

namespace Database\Seeders;

use App\Models\TenureBonusRule;
use Illuminate\Database\Seeder;

class TenureBonusRuleSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            [
                'working_days_start' => 0,
                'working_days_end' => 30,
                'bonus_amount' => 200,
                'is_completion_bonus' => false,
                'description' => 'First 30 working days bonus',
            ],
            [
                'working_days_start' => 31,
                'working_days_end' => 60,
                'bonus_amount' => 200,
                'is_completion_bonus' => false,
                'description' => 'Next 30 working days bonus',
            ],
            [
                'working_days_start' => 61,
                'working_days_end' => 90,
                'bonus_amount' => 300,
                'is_completion_bonus' => false,
                'description' => 'Next 30 working days bonus',
            ],
            [
                'working_days_start' => 91,
                'working_days_end' => 120,
                'bonus_amount' => 400,
                'is_completion_bonus' => false,
                'description' => 'Next 30 working days bonus',
            ],
            [
                'working_days_start' => 365,
                'working_days_end' => 365,
                'bonus_amount' => 2000,
                'is_completion_bonus' => true,
                'description' => 'Completion of 365 working days bonus',
            ],
        ];

        foreach ($rules as $rule) {
            TenureBonusRule::create($rule);
        }
    }
}
