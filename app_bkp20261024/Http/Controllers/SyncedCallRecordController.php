<?php

namespace App\Http\Controllers;

use App\Models\CallRecord;
use App\Models\CallRecordDisposition;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SyncedCallRecordController extends Controller
{
    private array $dispositions = [
        'success',
        'failed',
        'blank',
    ];

    private array $subDispositions = [
        'willing to join',
        'call back',
        'no response',
        'salary too low',
        'work hours too high',
    ];

    public function index(Request $request): View
    {
        $pageTitle = 'Synced Call Records';
        $callTypes = CallRecord::query()
            ->select('call_type')
            ->distinct()
            ->orderBy('call_type')
            ->pluck('call_type');
        $defaultDateRange = $request->input('date_range')
            ?? now()->subDays(29)->format('Y-m-d') . ' to ' . now()->format('Y-m-d');
        $statsQuery = CallRecord::query();

        $this->applyCallRecordFilters(
            $statsQuery,
            $request->input('call_type'),
            $defaultDateRange
        );

        $callTypeStats = [
            'incoming' => (clone $statsQuery)->where('call_type', 'incoming')->count(),
            'outgoing' => (clone $statsQuery)->where('call_type', 'outgoing')->count(),
            'missed' => (clone $statsQuery)->where('call_type', 'missed')->count(),
        ];

        return view('dashboard.synced-call-records.index', compact('pageTitle', 'callTypes', 'defaultDateRange', 'callTypeStats'));
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = CallRecord::query()
            ->join('devices', 'devices.id', '=', 'call_records.device_id')
            ->select([
                'call_records.id',
                'call_records.phone_number',
                'call_records.call_type',
                'call_records.duration',
                'call_records.call_timestamp',
            ])
            ->orderByDesc('call_records.call_timestamp');

        return DataTables::eloquent($query)
            ->filter(function ($query) use ($request) {
                $this->applyCallRecordFilters(
                    $query,
                    $request->input('filter_call_type'),
                    $request->input('filter_date_range')
                );

                $searchValue = $request->input('search.value');

                if (! empty($searchValue)) {
                    $query->where(function ($searchQuery) use ($searchValue) {
                        $searchQuery->where('call_records.phone_number', 'like', "%{$searchValue}%")
                            ->orWhere('call_records.call_type', 'like', "%{$searchValue}%")
                            ->orWhereExists(function ($existsQuery) use ($searchValue) {
                                $existsQuery->selectRaw('1')
                                    ->from('call_record_dispositions')
                                    ->whereColumn('call_record_dispositions.call_record_id', 'call_records.id')
                                    ->where(function ($dispositionQuery) use ($searchValue) {
                                        $dispositionQuery->where('call_record_dispositions.disposition', 'like', "%{$searchValue}%")
                                            ->orWhere('call_record_dispositions.sub_disposition', 'like', "%{$searchValue}%")
                                            ->orWhere('call_record_dispositions.remarks', 'like', "%{$searchValue}%");
                                    });
                            });
                    });
                }
            }, true)
            ->editColumn('duration', function ($row) {
                $duration = (int) $row->duration;
                $minutes = intdiv($duration, 60);
                $seconds = $duration % 60;

                return [
                    'display' => str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $seconds, 2, '0', STR_PAD_LEFT),
                    'sort' => $duration,
                ];
            })
            ->editColumn('call_timestamp', function ($row) {
                if (! $row->call_timestamp) {
                    return [
                        'display' => '',
                        'sort' => 0,
                    ];
                }

                $timestamp = Carbon::parse($row->getRawOriginal('call_timestamp'), 'UTC')->timezone('Asia/Kolkata');

                return [
                    'display' => $timestamp->format('d M Y h:i A'),
                    'sort' => strtotime($timestamp->toDateTimeString()),
                ];
            })
            ->make(true);
    }

    private function applyCallRecordFilters($query, ?string $callType, ?string $dateRange): void
    {
        if (! empty($callType) && $callType !== 'all') {
            $query->where('call_records.call_type', $callType);
        }

        $effectiveDateRange = $dateRange
            ?: now()->subDays(29)->format('Y-m-d') . ' to ' . now()->format('Y-m-d');

        $dates = explode(' to ', $effectiveDateRange);

        if (count($dates) === 2) {
            $startDate = Carbon::parse($dates[0])->startOfDay();
            $endDate = Carbon::parse($dates[1])->endOfDay();

            $query->whereBetween('call_records.call_timestamp', [$startDate, $endDate]);
        }
    }

    public function show(CallRecord $callRecord): View
    {
        $callRecord->load([
            'device',
            'dispositions.disposedBy',
            'latestDisposition.disposedBy',
        ]);

        $pageTitle = 'Lead Details';
        $dispositions = $this->dispositions;
        $subDispositions = $this->subDispositions;

        return view('dashboard.synced-call-records.show', compact('pageTitle', 'callRecord', 'dispositions', 'subDispositions'));
    }

    public function storeDisposition(Request $request, CallRecord $callRecord): JsonResponse
    {
        $validated = $request->validate([
            'disposition' => ['required', 'string', Rule::in($this->dispositions)],
            'sub_disposition' => ['nullable', 'string', Rule::in($this->subDispositions)],
            'remarks' => ['nullable', 'string'],
        ]);

        $disposition = $callRecord->dispositions()->create([
            'disposition' => $validated['disposition'],
            'sub_disposition' => $validated['sub_disposition'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'disposed_by' => Auth::id(),
            'disposed_at' => now(),
        ]);

        $disposition->load('disposedBy');

        return response()->json([
            'message' => 'Lead disposition saved successfully.',
            'disposition' => [
                'disposition' => $disposition->disposition,
                'sub_disposition' => $disposition->sub_disposition,
                'remarks' => $disposition->remarks,
                'disposed_at' => $disposition->disposed_at?->timezone('Asia/Kolkata')->format('d M Y h:i A'),
                'disposed_by' => $disposition->disposedBy?->name,
            ],
        ]);
    }
}
