<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryLineAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\SubdepartmentFactory> */
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'line_id',
        'shift_id',
        'start_date',
        'end_date',
        'process_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
