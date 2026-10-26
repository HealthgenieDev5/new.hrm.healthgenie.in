<?php

namespace Database\Seeders;

use App\Models\RetentionGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RetentionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $activeGroups = [
            ['label' => 'Active 1–15 days', 'min' => 0, 'max' => 15, 'points' => 55],
            ['label' => 'Active 16–31 days', 'min' => 16, 'max' => 31, 'points' => 60],
            ['label' => 'Active 1–2 months', 'min' => 32, 'max' => 60, 'points' => 65],
            ['label' => 'Active 2–3 months', 'min' => 61, 'max' => 90, 'points' => 70],
            ['label' => 'Active 3–4 months', 'min' => 91, 'max' => 120, 'points' => 75],
            ['label' => 'Active 4–5 months', 'min' => 121, 'max' => 150, 'points' => 80],
            ['label' => 'Active 5–6 months', 'min' => 151, 'max' => 180, 'points' => 85],
            ['label' => 'Active 6–12 months', 'min' => 181, 'max' => 365, 'points' => 90],
            ['label' => 'Active 12–18 months', 'min' => 366, 'max' => 547, 'points' => 95],
            ['label' => 'Active 18–24 months', 'min' => 548, 'max' => 730, 'points' => 95],
            ['label' => 'Active More than 24 months', 'min' => 731, 'max' => 10000, 'points' => 100],
        ];
        $leftGroups = [
            ['label' => 'Left same day', 'min' => 0, 'max' => 0, 'points' => 0],
            ['label' => 'Left in 1–15 days', 'min' => 1, 'max' => 15, 'points' => 5],
            ['label' => 'Left in 16–31 days', 'min' => 16, 'max' => 31, 'points' => 10],
            ['label' => 'Left in 1–2 months', 'min' => 32, 'max' => 60, 'points' => 15],
            ['label' => 'Left in 2–3 months', 'min' => 61, 'max' => 90, 'points' => 20],
            ['label' => 'Left in 3–4 months', 'min' => 91, 'max' => 120, 'points' => 25],
            ['label' => 'Left in 4–5 months', 'min' => 121, 'max' => 150, 'points' => 30],
            ['label' => 'Left in 5–6 months', 'min' => 151, 'max' => 180, 'points' => 35],
            ['label' => 'Left in 6–12 months', 'min' => 181, 'max' => 365, 'points' => 40],
            ['label' => 'Left in 12–18 months', 'min' => 366, 'max' => 547, 'points' => 45],
            ['label' => 'Left in 18–24 months', 'min' => 548, 'max' => 730, 'points' => 50],
            ['label' => 'Left after 24 months', 'min' => 731, 'max' => 10000, 'points' => 50],
        ];
        foreach ($activeGroups as $g) {
            RetentionGroup::create(array_merge($g, ['type' => 'active']));
        }
        foreach ($leftGroups as $g) {
            RetentionGroup::create(array_merge($g, ['type' => 'left']));
        }
    }
}
