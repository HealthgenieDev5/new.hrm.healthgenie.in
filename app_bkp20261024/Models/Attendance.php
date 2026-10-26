<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'empcode',
        'date',
        'intime',
        'outtime',
        'work_time',
        'over_time',
        'break_time',
        'status',
        'remark',
        'early_out',
        'late_in',
        'name',
        'shift_type',
    ];
}
