<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    /** @use HasFactory<\Database\Factories\LineFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function subdepartments()
    {
        return $this->belongsToMany(Subdepartment::class, 'subdepartment_line');
    }

    public function processes()
    {
        return $this->belongsToMany(Process::class, 'line_process');
    }

    public function getProcessescountAttribute()
    {
        return $this->processes()->count();
    }

    public function requirements()
    {
        return $this->hasMany(LineRequirement::class);
    }

    // public function workers()
    // {
    //     return $this->hasMany(Worker::class, 'line_id');
    // }
}
