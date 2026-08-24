<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Psp;
use App\Models\Shift;
use App\Services\AttendancePdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceReport extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $pageTitle = "Attendance Report";

        if ($user->hasRole('User')) {
            return view('user-dashboard', compact('pageTitle'));
        }

        #Initialize variables
        $yearMonth = $request->input('yearmonth');
        if ($yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
        } else {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
        }
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $datesInMonth = collect();
        $currentDate = Carbon::now()->toDateString();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $datesInMonth->push(Carbon::create($year, $month, $i)->toDateString());
        }

        $psps = Psp::all();
        $shifts = Shift::all();
        return view('reports.attendance-report.index', compact('pageTitle', 'datesInMonth', 'currentDate', 'user', 'psps', 'shifts'));
    }

    public function generatePDF(Request $request)
    {

        $AttendancePdfService = new AttendancePdfService();
        $result = $AttendancePdfService->getPdfData($request);

        $data = [
            'title'      => 'Attendance Report',
            'date'       => date('Y-m-d'),
            'pdfData'    => $result['workers'],
            'rangeStart' => $result['rangeStart'],
            'rangeEnd'   => $result['rangeEnd'],
        ];

        # Load Blade View and Pass Data
        $pdf = Pdf::setPaper('A4', 'portrait');

        if ($request->only_weekoff_working == 'yes') {
            $pdf->loadView('reports.attendance-report.pdfsample2', $data);
        } elseif (!$result['isFullMonth']) {
            $pdf->loadView('reports.attendance-report.pdfsample-daterange', $data);
        } else {
            $pdf->loadView('reports.attendance-report.pdfsample', $data);
        }

        # Return as Download
        return $pdf->download('sample.pdf');
    }
}
