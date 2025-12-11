<?php

namespace App\Http\Controllers;

use App\Models\ProcessedAttendance;
use App\Models\Worker;
use App\Models\TenureBonusRule;
use App\Models\WorkerTenureBonus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Services\TenureBonusService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TenureBonusController extends Controller
{
    protected TenureBonusService $tenureBonusService;

    public function __construct(TenureBonusService $tenureBonusService)
    {
        $this->tenureBonusService = $tenureBonusService;
    }


    public function indexBkp(Request $request)
    {
        $pageTitle = 'Tenure Bonuses';
        $search = $request->input('search');
        $bonusRules = TenureBonusRule::where('is_active', true)->get();
        $pendingBonuses = WorkerTenureBonus::whereHas('worker', function ($q) use ($search) {
            if ($search) {
                $q->where('name', 'like', "%$search%");
            }
        })
            ->with(['worker', 'bonusRule'])
            ->where('is_paid', false)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('tenure-bonus.index', compact('bonusRules', 'pendingBonuses', 'pageTitle'));
    }

    public function index(Request $request)
    {
        $pageTitle = 'Tenure Incentive';
        $selectedMonthYear = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($selectedMonthYear)->startOfMonth();
        $endDate = Carbon::parse($selectedMonthYear)->endOfMonth();
        $bonusRules = TenureBonusRule::where('is_active', true)->get();
        $bonuses = WorkerTenureBonus::with(['worker', 'bonusRule', 'worker.psp'])
            ->whereBetween('actual_eligibility_date', [$startDate, $endDate])
            ->orderBy('actual_eligibility_date', 'desc')
            ->get();
        // dd($bonuses);
        // die;
        $summary = [
            // 'total_bonuses' => $bonuses->total(),
            'total_bonuses' => $bonuses->count(),
            'total_amount' => $bonuses->sum('bonus_amount'),
            'paid_bonuses' => $bonuses->where('is_paid', 'yes')->count(),
            'paid_amount' => $bonuses->where('is_paid', 'yes')->sum('bonus_amount'),
            'pending_bonuses' => $bonuses->where('is_paid', 'pending')->count(),
            'pending_amount' => $bonuses->where('is_paid', 'pending')->sum('bonus_amount'),
            'unpaid_bonuses' => $bonuses->where('is_paid', 'no')->count(),
            'unpaid_amount' => $bonuses->where('is_paid', 'no')->sum('bonus_amount'),
            // PSP Worker Bonuses
            'psp_bonuses'      => $bonuses->where('is_psp_worker', true)->count(),
            'psp_amount'       => $bonuses->where('is_psp_worker', true)->sum('bonus_amount'),
        ];
        // dd($summary, $selectedMonthYear, $pageTitle, $bonuses);

        return view('tenure-bonus.index', compact('bonusRules', 'bonuses', 'summary', 'selectedMonthYear', 'pageTitle'));
    }

    public function processBonuses()
    {

        $workers = Worker::where('employment_status', 'Active')->get();
        $bonusRules = TenureBonusRule::where('is_active', true)->get();
        $processedCount = 0;

        foreach ($workers as $worker) {
            //$joiningDate = Carbon::parse($worker->date_of_joining);

            foreach ($bonusRules as $rule) {
                // Calculate actual eligibility date for this specific rule milestone
                $actualEligibilityDateForRule = $this->tenureBonusService->calculateEligibilityActualDate($worker, $rule->working_days_start);
            }
        }

        return redirect()->route('reports.tenure-bonus.index')
            ->with('success', "Processed {$processedCount} new bonuses successfully!");
    }

    // public function markAsPaid(Request $request, WorkerTenureBonus $bonus)
    // {
    //     $bonus->update([
    //         'is_paid' => 'yes',
    //         'paid_date' => now(),
    //         'payment_reference' => $request->payment_reference ?? "Paid by " . (Auth::check() ? Auth::user()->name : 'System'),
    //     ]);

    //     return redirect()->back()
    //         ->with('success', 'Bonus marked as paid successfully!');
    // }


    public function markAsPaidBulk(Request $request)
    {
        $validated = $request->validate([
            'keys' => 'required|array',
            'keys.*' => 'required|integer'
        ]);
        //$paymentReference = $request->payment_reference . " Updated by " . (Auth::check() ? Auth::user()->name : 'System') . ' On ' . now()->format('Y-m-d');
        $paymentReference = sprintf(
            "%s (Updated by %s)",
            $request->payment_reference ?? 'No Ref',
            Auth::check() ? Auth::user()->name : 'System'
        );

        $count = WorkerTenureBonus::whereIn('id', $validated['keys'])
            ->update([
                'is_paid' => 'yes',
                'paid_date' => now(),
                'payment_reference' => $paymentReference,
            ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => "{$count} bonuses have been marked as paid successfully."
        // ], 200);
        return redirect()->back()->with('success', "{$count} bonuses have been marked as paid successfully.");
    }





    public function markUnpaid(Request $request, WorkerTenureBonus $bonus)
    {
        $paymentReference = "Unpaid by " . (Auth::check() ? Auth::user()->name : 'System') . " - {$request->unpaid_reason} - {$request->unpaid_notes} - " . now();
        //$paymentReference = "Unpaid by HR - {$bonus->unpaid_reason} - {$bonus->unpaid_notes} - {$bonus->paid_date} - {$bonus->paid_by}";
        $bonus->update([
            'is_paid' => 'no',
            'paid_date' => null,
            'payment_reference' => $paymentReference,
        ]);

        return redirect()->back()
            ->with('success', 'Bonus marked as unpaid successfully!');
    }



    public function markUnpaidBulk(Request $request)
    {
        $request->validate([
            'keys' => 'required|array',
            'keys.*' => 'exists:worker_tenure_bonuses,id'
        ]);

        $bonusIds = $request->keys;
        //$paymentReference = "Bulk Unpaid by " . (Auth::check() ? Auth::user()->name : 'System') . " - " . now()->format('Y-m-d H:i:s');
        $paymentReference = sprintf(
            "Reason: %s | Notes: %s (Updated by %s)",
            $request->unpaid_reason ?? 'N/A',
            $request->unpaid_notes ?? 'N/A',
            Auth::check() ? Auth::user()->name : 'System'
        );

        $count = WorkerTenureBonus::whereIn('id', $bonusIds)
            ->where('is_paid', 'pending')
            ->update([
                'is_paid' => 'no',
                'paid_date' => null,
                'payment_reference' => $paymentReference,
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$count} bonuses have been marked as unpaid successfully."
        ], 200);
    }

    public function markAsPending(Request $request, WorkerTenureBonus $bonus)
    {
        $bonus->update([
            'is_paid' => 'pending',
            'paid_date' => null,
            'payment_reference' => "Reset to pending by " . (Auth::check() ? Auth::user()->name : 'System') . " - " . now(),
        ]);

        return redirect()->back()
            ->with('success', 'Bonus marked as pending successfully!');
    }
}
