<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetentionGroup extends Model
{
    /** @use HasFactory<\Database\Factories\PspFactory> */
    use HasFactory;

    protected $fillable = [
        'label',
        'min',
        'max',
        'points',
        'type',
    ];
}
