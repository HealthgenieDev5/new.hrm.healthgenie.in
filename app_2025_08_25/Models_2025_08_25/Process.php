<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    /** @use HasFactory<\Database\Factories\ProcessFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_process');
    }
}
