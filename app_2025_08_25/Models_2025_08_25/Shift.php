<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    /** @use HasFactory<\Database\Factories\ShiftFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'in_time',
        'out_time',
        'week_off',
        'grace_minutes',
        'shift_type',
        'sub_type',
        'sister_shift',
        'is_internal',
    ];

    public function sisterShift()
    {
        return $this->belongsTo(Shift::class, 'sister_shift');
    }
}
