<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryMaster extends Model
{
    /** @use HasFactory<\Database\Factories\SalaryMasterFactory> */
    use HasFactory;

    protected $fillable = [
        'worker_id', // Link Salary to Worker
        'basic',
        'hra',
        'conveyance',
        'medical_allowance',
        'special_allowance',
        'fuel_allowance',
        'vacation_allowance',
        'other_allowance',
        'bonus_allowed',
        'pf_allowed',
        'esic_allowed'
    ];

    // 🔥 CTC Calculation (Dynamic)
    public function getCtcAttribute()
    {
        return $this->gross + $this->employer_pf + $this->employer_esi + $this->bonus;
    }

    // 🔥 Gross Salary Calculation (Dynamic)
    public function getGrossAttribute()
    {
        return $this->basic + $this->hra + $this->conveyance + $this->medical_allowance +
            $this->special_allowance + $this->fuel_allowance + $this->vacation_allowance +
            $this->other_allowance;
    }

    // 🔥 Bonus Calculation (Dynamic)
    public function getBonusAttribute()
    {
        if ($this->bonus_allowed) {
            return ($this->worker?->workerType?->minimum_wage * 8.33) / 100;
        }
        return 0;
    }

    // 🔥 Employee PF Calculation (Dynamic)
    public function getEmployeePfAttribute()
    {
        if ($this->pf_allowed) {
            $pf_base = ($this->basic >= 15000 || ($this->gross - $this->hra) >= 15000) ? 15000 : ($this->gross - $this->hra);
            return ($pf_base * 12) / 100;
        }
        return 0;
    }

    // 🔥 Employer PF Calculation (Dynamic)
    public function getEmployerPfAttribute()
    {
        if ($this->pf_allowed) {
            $pf_base = ($this->basic >= 15000 || ($this->gross - $this->hra) >= 15000) ? 15000 : ($this->gross - $this->hra);
            return ($pf_base * 13) / 100;
        }
        return 0;
    }

    // 🔥 Employee ESI Calculation (Dynamic)
    public function getEmployeeEsicAttribute()
    {
        return $this->esic_allowed ? ($this->gross * 0.75) / 100 : 0;
    }

    // 🔥 Employer ESI Calculation (Dynamic)
    public function getEmployerEsicAttribute()
    {
        return $this->esic_allowed ? ($this->gross * 3.25) / 100 : 0;
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
