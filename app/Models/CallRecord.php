<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CallRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'phone_number',
        'call_type',
        'duration',
        'call_timestamp',
    ];

    protected $casts = [
        'duration' => 'integer',
        'call_timestamp' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function dispositions(): HasMany
    {
        return $this->hasMany(CallRecordDisposition::class)->orderByDesc('disposed_at');
    }

    public function latestDisposition(): HasOne
    {
        return $this->hasOne(CallRecordDisposition::class)->latestOfMany('disposed_at');
    }
}
