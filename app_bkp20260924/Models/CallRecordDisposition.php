<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallRecordDisposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'call_record_id',
        'disposition',
        'sub_disposition',
        'remarks',
        'disposed_by',
        'disposed_at',
    ];

    protected $casts = [
        'disposed_at' => 'datetime',
    ];

    public function callRecord(): BelongsTo
    {
        return $this->belongsTo(CallRecord::class);
    }

    public function disposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }
}
