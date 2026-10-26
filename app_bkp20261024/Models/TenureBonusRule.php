<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenureBonusRule extends Model
{
    protected $fillable = [
        'days_completed',
        'bonus_amount',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bonus_amount' => 'decimal:2',
    ];

    public function workerTenureBonuses(): HasMany
    {
        return $this->hasMany(WorkerTenureBonus::class);
    }
}
