<?php

namespace App\Models;

use App\Models\Scopes\WorkerVisibilityScope;
use App\Models\Traits\WorkerScopes;
use App\Traits\Revisionable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Worker extends Model
{
    /** @use HasFactory<\Database\Factories\WorkerFactory> */
    use HasFactory, Revisionable, SoftDeletes, WorkerScopes;

    protected $fillable = [
        'code',
        'psp_id',
        'photo',
        'employment_status',
        'name',
        'gender',
        'shift_id',
        'father_name',
        'husband_name',
        'date_of_birth',
        'religion',
        'date_of_joining',
        'date_of_leaving',
        'mobile_number',
        'uan',
        'uan_number',
        'esic',
        'esic_number',
        'marital_status',
        'permanent_address',
        'permanent_pincode',
        'permanent_district',
        'permanent_state',
        'present_address',
        'present_pincode',
        'present_district',
        'present_state',
        'adhar_number',
        'adhar_front',
        'adhar_back',
        'uan_photo',
        'form_11_photo',
        'affidavit_photo',
        'lop_photo',


        'designation_id',
        'level_id',
        'workertype_id',


        'division_id',
        'department_id',
        'subdepartment_id',
        'working_area_id',
        'production_product_id',
        'line_id',
        'process_id',


        'ceated_by',
        'deleted_by',
    ];


    protected static function booted()
    {
        static::addGlobalScope(new WorkerVisibilityScope(app(User::class)));

        static::creating(function ($worker) {
            $worker->created_by = Auth::id();

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if ($user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
                $psp = Psp::find($worker->psp_id);
                $worker->code = $worker->generateCode($psp);
            }

            if ($user->hasRole('PSP')) {
                $worker->code = $worker->generateCode();
                $worker->psp_id = Auth::user()->psp->id;
            }
        });

        static::deleting(function ($worker) {
            if (!$worker->isForceDeleting()) {
                $worker->deleted_by = Auth::id();
                $worker->saveQuietly(); // Save without triggering events
            }
        });
    }

    public function creater()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function generateCode($psp = null)
    {
        $psp = $psp ?? Auth::user()->psp;
        if (!$psp) {
            throw new \Exception('PSP is required to generate worker code.');
        }
        #Get the PSP's code
        $pspCode = $psp->code;

        #Fetch the highest code for this PSP
        $highestCode = self::withTrashed()
            ->where('psp_id', $psp->id)
            ->where('code', 'like', "$pspCode%")
            ->orderByRaw('CAST(SUBSTRING(code, LENGTH(?) + 1) AS UNSIGNED) DESC', [$pspCode])
            ->value('code');

        #Extract the numeric part and increment
        $nextNumber = $highestCode
            ? ((int) substr($highestCode, strlen($pspCode)) + 1)
            : 1;

        #Return the new code
        return $pspCode . $nextNumber;
    }

    public function psp()
    {
        return $this->belongsTo(Psp::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function holidays()
    {
        return $this->belongsToMany(Holiday::class, 'worker_holiday');
    }

    public function processedAttendance()
    {
        return $this->hasMany(ProcessedAttendance::class, 'worker_id', 'id');
    }

    public function salaryStructure()
    {
        return $this->hasOne(SalaryMaster::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function workertype()
    {
        return $this->belongsTo(Workertype::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subdepartment()
    {
        return $this->belongsTo(Subdepartment::class);
    }

    public function workingArea()
    {
        return $this->belongsTo(WorkingArea::class);
    }

    public function productionProduct()
    {
        return $this->belongsTo(ProductionProduct::class);
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function ShiftOverride()
    {
        return $this->hasOne(ShiftOverride::class);
    }
}
