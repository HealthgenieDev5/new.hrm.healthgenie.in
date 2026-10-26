<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ShiftOverride extends Model
{
    /** @use HasFactory<\Database\Factories\ShiftOverridesFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'worker_id',
        'shift_id',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::creating(function ($shiftoverride) {
            $shiftoverride->created_by = Auth::id();
        });

        static::updating(function ($shiftoverride) {
            $shiftoverride->updated_by = Auth::id();
        });

        static::deleting(function ($shiftoverride) {
            if (!$shiftoverride->isForceDeleting()) {
                $shiftoverride->deleted_by = Auth::id();
                $shiftoverride->saveQuietly(); // Save without triggering events
            }
        });
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
