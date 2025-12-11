<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\Psp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RetentionGroup;
use Illuminate\Support\Facades\Auth;

class WorkerRetentionController extends Controller
{
    private $user;
    public function __construct()
    {
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }

    public function index()
    {
        $today = Carbon::today();

        // Get all PSPs
        // $psps = Psp::with('workers')->get();
        if ($this->user->hasRole('PSP')) {
            $pspId = $this->user->psp->id;
            $psps = Psp::with('workers')->where('id', $pspId)->get();
        } else {
            $psps = Psp::with('workers')->get();
        }

        $activeGroups = RetentionGroup::where('type', 'active')->orderBy('min')->get();
        $leftGroups = RetentionGroup::where('type', 'left')->orderBy('min')->get();

        $pspStats = [];

        foreach ($psps as $psp) {
            $workers = $psp->workers;
            $activeWorkers = $workers->whereNull('date_of_leaving');
            $leftWorkers = $workers->whereNotNull('date_of_leaving');

            // Active
            $activeStats = [];
            foreach ($activeGroups as $group) {
                $count = $activeWorkers->filter(function ($worker) use ($group, $today) {
                    $joiningDate = $worker->date_of_joining ?? $worker->created_at;
                    $days = Carbon::parse($joiningDate)->diffInDays($today);
                    return $days >= $group->min && $days <= $group->max;
                })->count();
                $activeStats[] = [
                    'psp' => $psp->name,
                    'group' => $group->label,
                    'count' => $count,
                    'percentage' => $activeWorkers->count() ? round(($count / $activeWorkers->count()) * 100, 2) : 0,
                    'weighted' => $activeWorkers->count() ? round((($count / $activeWorkers->count()) * $group->points), 2) : 0,
                ];
            }

            // Left
            $leftStats = [];
            foreach ($leftGroups as $group) {
                $count = $leftWorkers->filter(function ($worker) use ($group) {
                    $joiningDate = $worker->date_of_joining ?? $worker->created_at;
                    $days = Carbon::parse($joiningDate)->diffInDays(Carbon::parse($worker->date_of_leaving));
                    return $days >= $group->min && $days <= $group->max;
                })->count();
                $leftStats[] = [
                    'psp' => $psp->name,
                    'group' => $group->label,
                    'count' => $count,
                    'percentage' => $leftWorkers->count() ? round(($count / $leftWorkers->count()) * 100, 2) : 0,
                    'weighted' => $leftWorkers->count() ? round((($count / $leftWorkers->count()) * $group->points), 2) : 0,
                ];
            }

            $pspStats[] = [
                'psp' => $psp->name,
                'activeStats' => $activeStats,
                'leftStats' => $leftStats,
            ];
        }

        $pageTitle = "Retention Report";
        $allGroups = RetentionGroup::orderBy('type')->orderBy('min')->get();

        return view('master.psps.retention', compact('pspStats', 'pageTitle', 'allGroups'));
    }
}
