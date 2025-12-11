<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineRequirement extends Model
{
    use HasFactory;
    protected $fillable = [
        'line_id',
        'date',
        'workers',
        'extras',
        'supervisors',
        'qas',
        'services',
        'movers',
        'shift_type',
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }
}
