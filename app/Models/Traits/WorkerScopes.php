<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait WorkerScopes
{
    public function scopeActiveBetween(Builder $query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate) {
            $q->whereNull('date_of_leaving')
                ->orWhere('date_of_leaving', '>=', $startDate);
        })->where(function ($q) use ($endDate) {
            $q->whereNull('date_of_joining')
                ->orWhere('date_of_joining', '<=', $endDate);
        });
    }

    // Add more reusable scopes here later

    public function scopeActive(Builder $query)
    {
        return $query->whereNull('date_of_leaving')
            ->orWhere('date_of_leaving', '>=', now()->toDateString());
    }
}
