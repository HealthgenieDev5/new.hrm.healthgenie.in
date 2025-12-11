<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psp extends Model
{
    /** @use HasFactory<\Database\Factories\PspFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'address',
        'city',
        'state',
        'pincode',
        'mobile_number',
        'alternate_number',
        'email_id',
        'contact_person',
        'code',
        'user_id',
        'day_worker_target',
        'night_worker_target',
    ];

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }

    public function getPsp()
    {
        return $this->with('workers')->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
