<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProcessedSalary extends Model
{
    /** @use HasFactory<\Database\Factories\ProcessedSalaryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'year',
        'month',
        'basic',
        'hra',
        'conveyance',
        'medical_allowance',
        'special_allowance',
        'fuel_allowance',
        'vacation_allowance',
        'other_allowance',
        'gross',
        'pf_employee',
        'pf_employer',
        'esic_employee',
        'esic_employer',
        'bonus',
        'net_salary',
        'salary_structure',
        'paid_days',
        'updated_by',    // Tracks last user who updated
        'deleted_by',    // Tracks user who deleted (if soft deleted)
    ];

    protected $casts = [
        'basic'             => 'decimal:2',
        'hra'               => 'decimal:2',
        'conveyance'        => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'fuel_allowance'    => 'decimal:2',
        'vacation_allowance' => 'decimal:2',
        'other_allowance'   => 'decimal:2',
        'gross'             => 'decimal:2',
        'pf_employee'       => 'decimal:2',
        'pf_employer'       => 'decimal:2',
        'esic_employee'     => 'decimal:2',
        'esic_employer'     => 'decimal:2',
        'bonus'             => 'decimal:2',
        'net_salary'        => 'decimal:2',
        'salary_structure'  => 'object',
        'paid_days'  => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        #Automatically assign updated_by on create & update
        static::creating(function ($salary) {
            $salary->updated_by = Auth::id();
        });

        static::updating(function ($salary) {
            $salary->updated_by = Auth::id();
        });

        #Automatically assign deleted_by before soft delete
        static::deleting(function ($salary) {
            if ($salary->isSoftDeleting()) {
                $salary->deleted_by = Auth::id();
                $salary->saveQuietly(); #Avoid triggering update events
            }
        });
    }


    /**
     * Relationship: ProcessedSalary belongs to a Worker.
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
