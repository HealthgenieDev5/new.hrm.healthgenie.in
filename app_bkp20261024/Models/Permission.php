<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected $fillable = ['name'];

    // Define the many-to-many relationship with roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
    }
}
