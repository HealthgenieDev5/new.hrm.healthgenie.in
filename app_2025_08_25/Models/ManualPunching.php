<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ManualPunching extends Model
{
    /** @use HasFactory<\Database\Factories\ManualPunchingFactory> */
    use HasFactory;

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'worker_id',
        'shift_type',
        'date',
        'intime',
        'outtime',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected static function booted()
    {
        static::creating(function ($manualPunching) {
            $manualPunching->created_by = Auth::id();
        });

        static::updating(function ($manualPunching) {
            $manualPunching->updated_by = Auth::id();
        });

        static::deleting(function ($manualPunching) {
            if (!$manualPunching->isForceDeleting()) {
                $manualPunching->deleted_by = Auth::id();
                $manualPunching->saveQuietly(); // Save without triggering events
            }
        });
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
