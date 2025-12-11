<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'division_department');
    }

    public function subdepartments()
    {
        return $this->belongsToMany(Subdepartment::class, 'department_subdepartment');
    }

    public function getSubdepartmentscountAttribute()
    {
        return $this->subdepartments()->count();
    }
}
