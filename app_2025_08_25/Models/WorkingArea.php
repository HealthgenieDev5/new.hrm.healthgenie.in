<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingArea extends Model
{
    /** @use HasFactory<\Database\Factories\WorkingAreaFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
