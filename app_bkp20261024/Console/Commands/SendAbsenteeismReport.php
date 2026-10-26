<?php

namespace App\Console\Commands;

use App\Mail\AbsenteesmMail;
use App\Mail\NoAbsenteesMail;
use App\Models\ProcessedAttendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbsenteeismReport extends Command
{
    protected $signature = 'mail:send-absenteeism-report';

    protected $description = 'Send daily absenteeism report email to HR (alerts if 3+ day absentees found, congratulates if none)';

    public function handle(): int
    {
        $this->info('Sending absenteeism report...');

        try {
            $today = Carbon::today();
            $threeDaysAgo = $today->copy()->subDays(2); // today minus 2 = last 3 days inclusive

            // Get active workers who have joined on or before today, excluding Heuer International (psp_id 11)
            $activeWorkerIds = Worker::where('employment_status', 'Active')
                ->whereDate('date_of_joining', '<=', $today)
                ->where('psp_id', '!=', 11)
                ->pluck('id');

            // Find workers who are absent on ALL days from 3 days ago to today
            $absentWorkerIds = ProcessedAttendance::whereIn('worker_id', $activeWorkerIds)
                ->whereBetween('date', [$threeDaysAgo, $today])
                ->where('is_absent', true)
                ->groupBy('worker_id')
                ->havingRaw('COUNT(*) >= 3')
                ->pluck('worker_id');

            // Get last present date for each absent worker
            $lastPresentDates = ProcessedAttendance::whereIn('worker_id', $absentWorkerIds)
                ->where('is_absent', false)
                ->where('status', 'P')
                ->groupBy('worker_id')
                ->selectRaw('worker_id, MAX(date) as last_present_date')
                ->pluck('last_present_date', 'worker_id');

            // Build final list with worker details
            $absentees = Worker::whereIn('id', $absentWorkerIds)
                ->get(['id', 'name', 'code', 'mobile_number'])
                ->map(function ($worker) use ($lastPresentDates) {
                    return [
                        'name' => $worker->name,
                        'employee_code' => $worker->code,
                        'mobile_number' => $worker->mobile_number,
                        'last_present_date' => isset($lastPresentDates[$worker->id])
                            ? Carbon::parse($lastPresentDates[$worker->id])->format('d M Y')
                            : 'N/A',
                    ];
                });

            $fromDate = $threeDaysAgo->format('d M Y');
            $toDate = $today->format('d M Y');

            // $to = ['hrd@gstc.com', 'careers@gstc.com', 'payroll@gstc.com'];
            $to = ['hrd@heuerinternational.com'];
            $cc = ['hrd@gstc.com', 'payroll1@gstc.com', 'payroll@gstc.com', 'developer3@healthgenie.in', 'developer2@healthgenie.in'];

            if ($absentees->count() > 0) {
                Mail::to($to)->cc($cc)->send(new AbsenteesmMail([
                    'absentees' => $absentees,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'total' => $absentees->count(),
                ]));

                $this->warn("Alert sent — {$absentees->count()} worker(s) absent for 3+ days.");
                Log::info("SendAbsenteeismReport: Alert sent — {$absentees->count()} absentee(s).");
            } else {
                Mail::to($to)->cc($cc)->send(new NoAbsenteesMail([
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'total_active' => $activeWorkerIds->count(),
                ]));

                $this->info('Congratulation email sent — no long-term absentees today.');
                Log::info('SendAbsenteeismReport: Congratulation email sent — zero absentees.');
            }
        } catch (\Throwable $e) {
            $this->error('Failed to send absenteeism report: ' . $e->getMessage());
            Log::error('SendAbsenteeismReport failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
