<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'old_values',
        'new_values',
        'user_id',
    ];

    /**
     * Define the morph relationship to the revisionable model.
     */
    public function revisionable()
    {
        return $this->morphTo();
    }

    /**
     * Define the relationship to the user who made the revision.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
