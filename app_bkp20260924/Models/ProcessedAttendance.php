<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedAttendance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'processed_attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'worker_id',
        'date',

        'shift_start',
        'shift_end',
        'shift_type',

        'in_time',
        'out_time',
        'in_time_processed',
        'out_time_processed',
        'late_coming_minutes',
        'early_going_minutes',

        'adjustable_minutes',
        'adjusted_minutes',

        'is_missed_punch',
        'is_absent',
        'is_holiday',
        'is_weekoff',

        'shift_hours',
        'total_work_hours',
        'work_hours_within_shift',
        'grace_minutes',

        'status',
        'status_remarks',


    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'in_time' => 'datetime:H:i',
        'out_time' => 'datetime:H:i',
        'in_time_processed' => 'datetime:H:i',
        'out_time_processed' => 'datetime:H:i',
        'is_missed_punch' => 'boolean',
        'is_absent' => 'boolean',
    ];

    /**
     * Relationship: Link processed attendance to the worker.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class, 'date', 'date');
    }
}
