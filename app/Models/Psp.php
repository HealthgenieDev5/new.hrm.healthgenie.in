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
        'is_active',
        'deactivated_at',
        'deactivated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deactivated_at' => 'datetime',
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

    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    /**
     * Blade view for the attendance PDF header block, fixed per PSP via config/pdf_layouts.php.
     */
    public static function pdfHeaderViewFor(?Psp $psp): string
    {
        $default = (int) config('pdf_layouts.default', 1);
        $layout = $psp ? (int) config("pdf_layouts.psp.{$psp->id}", $default) : $default;

        if ($layout < 1 || $layout > (int) config('pdf_layouts.count', 1)) {
            $layout = $default;
        }

        return "reports.attendance-report.headers.layout-{$layout}";
    }
}
