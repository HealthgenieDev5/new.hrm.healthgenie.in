<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerTenureBonus extends Model
{
    protected $fillable = [
        'worker_id',
        'tenure_bonus_rule_id',
        'bonus_amount',
        'bonus_date',
        'actual_eligibility_date',
        'days_completed',
        'is_paid',
        'paid_date',
        'payment_reference',
        'remarks',
        'working_days_completed',
        'is_completion_bonus',
        'is_psp_worker',
    ];

    protected $casts = [
        'bonus_date' => 'datetime',
        'actual_eligibility_date' => 'datetime',
        'paid_date' => 'datetime',
        'is_completion_bonus' => 'boolean',
        'is_psp_worker' => 'boolean',
        'bonus_amount' => 'decimal:2',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function bonusRule(): BelongsTo
    {
        return $this->belongsTo(TenureBonusRule::class, 'tenure_bonus_rule_id');
    }
}
