<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdepartment extends Model
{
    /** @use HasFactory<\Database\Factories\SubdepartmentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_subdepartment');
    }

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'subdepartment_line');
    }

    public function getLinescountAttribute()
    {
        return $this->lines()->count();
    }
}
