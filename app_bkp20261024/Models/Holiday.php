<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    /** @use HasFactory<\Database\Factories\HolidayFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'date',
        'code',
    ];

    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'worker_holiday');
    }

    public function getWorkercountAttribute()
    {
        return $this->workers()->count();
    }

    public function getTypeAttribute()
    {
        switch ($this->code) {
            case 'HL':
                $type = "Holiday";
                break;

            case 'NH':
                $type = "National Holiday";
                break;


            case 'RH':
                $type = "Religious Holiday";
                break;


            case 'SPL HL':
                $type = "Special Holiday";
                break;

            default:
                $type = null;
                break;
        }
        return $type;
    }
}
