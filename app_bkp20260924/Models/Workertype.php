<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workertype extends Model
{
    /** @use HasFactory<\Database\Factories\WorkertypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'minimum_wage' // Allow updating Minimum Wages
    ];

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
}
