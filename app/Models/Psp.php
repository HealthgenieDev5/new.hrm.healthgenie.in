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
        return 'reports.attendance-report.headers.layout-' . self::pdfLayoutFor($psp);
    }

    /**
     * Attendance PDF layout number for the PSP, from config/pdf_layouts.php.
     */
    public static function pdfLayoutFor(?Psp $psp): int
    {
        $default = (int) config('pdf_layouts.default', 1);
        $layout = $psp ? (int) config("pdf_layouts.psp.{$psp->id}", $default) : $default;

        if ($layout < 1 || $layout > (int) config('pdf_layouts.count', 1)) {
            $layout = $default;
        }

        return $layout;
    }

    /**
     * CSS font-family for the PSP's attendance PDF; layouts without a font use dompdf's default.
     */
    public static function pdfFontFamilyFor(?Psp $psp): string
    {
        $family = config('pdf_layouts.fonts.' . self::pdfLayoutFor($psp) . '.family');

        return $family ? "'{$family}', serif" : 'serif';
    }

    /**
     * Inline CSS (font-family + line-height) for the PSP's attendance PDF blocks.
     */
    public static function pdfFontStyleFor(?Psp $psp): string
    {
        $style = 'font-family: ' . self::pdfFontFamilyFor($psp);
        $lineHeight = config('pdf_layouts.fonts.' . self::pdfLayoutFor($psp) . '.line_height');

        return $lineHeight ? "{$style}; line-height: {$lineHeight}" : $style;
    }

    /**
     * Fonts used by the attendance PDFs, with absolute file paths for @font-face.
     */
    public static function pdfFontFaces(): array
    {
        return collect(config('pdf_layouts.fonts', []))
            ->map(fn ($font) => [
                'family' => $font['family'],
                'regular' => resource_path('fonts/pdf/' . $font['regular']),
                'bold' => resource_path('fonts/pdf/' . $font['bold']),
            ])
            ->values()
            ->all();
    }
}
