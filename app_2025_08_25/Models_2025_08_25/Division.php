<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /** @use HasFactory<\Database\Factories\DivisionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'division_department');
    }

    public function getDepartmentcountAttribute()
    {
        return $this->departments()->count();
    }
}
