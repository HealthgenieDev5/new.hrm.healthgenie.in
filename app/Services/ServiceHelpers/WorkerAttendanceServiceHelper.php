<?php

namespace App\Services\ServiceHelpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WorkerAttendanceServiceHelper
{
    /** @var \App\Models\User|null $user */
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function countPresentOnWeekOff($attendanceRecords)
    {
        $count = 0;
        if (!$attendanceRecords) {
            return $count;
        }

        foreach ($attendanceRecords as $record) {
            if ($record['status'] == "P" && $record['is_weekoff']) {
                $count = $count + 1;
            }
            // elseif ($record['status'] == "H/D" && $record['is_weekoff']) {
            //     $count = $count + 0.5;
            // }
        }
        return $count;
    }

    public function countHalfDayOnWeekOff($attendanceRecords)
    {
        $count = 0;
        if (!$attendanceRecords) {
            return $count;
        }

        foreach ($attendanceRecords as $record) {
            // if ($record['status'] == "P" && $record['is_weekoff']) {
            //     $count = $count + 1;
            // }else
            if ($record['status'] == "H/D" && $record['is_weekoff']) {
                $count = $count + 1;
            }
        }
        return $count;
    }

    public function countSandwichOnWeekOff($attendanceRecords)
    {
        $count = 0;
        if (!$attendanceRecords) {
            return $count;
        }

        foreach ($attendanceRecords as $record) {
            if ($record['status'] == "S/W" && $record['is_weekoff']) {
                $count = $count + 1;
            }
        }
        return $count;
    }

    public function countTotalIsWeekOff($attendanceRecords)
    {
        $count = 0;
        if (!$attendanceRecords) {
            return $count;
        }

        foreach ($attendanceRecords as $record) {
            if ($record['is_weekoff']) {
                $count = $count + 1;
            }
        }
        return $count;
    }

    public function getPresentCount($allStatus)
    {
        return $allStatus ? array_reduce($allStatus, function ($carry, $status) {
            if (in_array($status, ['P'])) {
                $carry += 1;
            }
            // elseif (in_array($status, ['H/D'])) {
            //     $carry += 0.5;
            // }
            return $carry;
        }, 0) : 0;
    }

    public function getSandwichCount($allStatus)
    {
        return $allStatus ? array_reduce($allStatus, function ($carry, $status) {
            if (in_array($status, ['S/W'])) {
                $carry += 1;
            }
            // elseif (in_array($status, ['H/D'])) {
            //     $carry += 0.5;
            // }
            return $carry;
        }, 0) : 0;
    }

    public function getHalfDayCount($allStatus)
    {
        return $allStatus ? array_reduce($allStatus, function ($carry, $status) {
            // if (in_array($status, ['P'])) {
            //     $carry += 1;
            // } else

            if (in_array($status, ['H/D'])) {
                $carry += 1;
            }
            return $carry;
        }, 0) : 0;
    }

    public function getWeekOffCount($allStatus)
    {
        return $allStatus ? count(array_filter($allStatus, function ($status) {
            return in_array($status, ['W/O']);
        })) : 0;

        $status = $record?->is_weekoff && in_array($record?->status, ['P', 'H/D', 'A']) ? 'W/O' : $record?->status;
    }

    public function getHolidayAndRhCount($allStatus)
    {
        return $allStatus ? count(array_filter($allStatus, function ($status) {
            return in_array($status, ['HL', 'RH', 'NH', 'SPL HL']);
        })) : 0;
    }

    public function getMissedPunchCount($allStatus)
    {
        return $allStatus ? count(array_filter($allStatus, function ($status) {
            return in_array($status, ['M/P']);
        })) : 0;
    }

    public function getAbsentCount($allStatus)
    {
        // return $allStatus ? count(array_filter($allStatus, function ($status) {
        //     return in_array($status, ['A']);
        // })) : 0;

        return $allStatus ? array_reduce($allStatus, function ($carry, $status) {
            if (in_array($status, ['A'])) {
                $carry += 1;
            } elseif (in_array($status, ['H/D'])) {
                $carry += 0.5;
            }
            return $carry;
        }, 0) : 0;
    }

    public function getWrongShiftCount($allStatus)
    {
        return $allStatus ? count(array_filter($allStatus, function ($status) {
            return in_array($status, ['WRONG SHIFT']);
        })) : 0;
    }

    public function getPaidDaysCount($allStatus)
    {
        return $allStatus ? array_reduce($allStatus, function ($total, $status) {
            if (in_array($status, ['P', 'W/O', 'HL', 'RH', 'NH'])) {
                $total += 1; // Add 1 for full paid days
            } elseif ($status === 'H/D') {
                $total += 0.5; // Add 0.5 for half days
            }
            return $total;
        }, 0) : 0;
    }

    public function getTotalLateComing($allLateComings)
    {
        return $allLateComings ? array_sum($allLateComings) : 0;
    }

    public function getTotalEarlyGoing($allEarlyGoings)
    {
        return $allEarlyGoings ? array_sum($allEarlyGoings) : 0;
    }

    public function getTotalGrace($allGraceMinutes)
    {
        return $allGraceMinutes ? array_sum($allGraceMinutes) : 0;
    }

    public function getStatusHtml($attendance)
    {
        $textClass = $attendance?->status == "M/P" ? "text-danger" : "";
        return $attendance
            ? "<span class='badge badge-secondary bg-transparent px-2 rounded-bottom-0 border border-dashed border-accent {$textClass}'>{$attendance?->status}</span>"
            : "<span class='badge badge-light-warning px-2 rounded-bottom-0 border border-dashed border-accent'>N/A</span>";
    }

    public function getLateComingHtml($attendance)
    {
        if ($attendance) {
            $in_time_processed = $attendance?->in_time_processed ?? false;
            if ($in_time_processed && $attendance?->late_coming_minutes > 0) {
                return "<span class='badge px-2 badge-light-danger rounded-top-0 rounded-bottom-0 border border-dashed border-accent border-top-0'>Arrived {$attendance?->late_coming_minutes} mnt late</span>";
            } elseif ($in_time_processed && $attendance?->late_coming_minutes == 0) {
                return "<span class='badge px-2 badge-light-success rounded-top-0 rounded-bottom-0 border border-dashed border-accent border-top-0'>Arrived on time</span>";
            } else {
                return "<span class='badge px-2 badge-light-warning rounded-top-0 rounded-bottom-0 border border-dashed border-accent border-top-0'>&nbsp;</span>";
            }
        } else {
            return "<span class='badge px-2 badge-light-warning rounded-top-0 rounded-bottom-0 border border-dashed border-accent border-top-0'>No attendance</span>";
        }
    }

    public function getEarlyGoingHtml($attendance)
    {
        if ($attendance) {
            $out_time_processed = $attendance?->out_time_processed ?? false;
            if ($out_time_processed && $attendance?->early_going_minutes > 0) {
                return "<span class='badge px-2 badge-light-danger rounded-top-0 border border-dashed border-accent border-top-0'>Left {$attendance?->early_going_minutes} mnt early</span>";
            } elseif ($out_time_processed && $attendance?->early_going_minutes == 0) {
                return "<span class='badge px-2 badge-light-success rounded-top-0 border border-dashed border-accent border-top-0'>Left on time</span>";
            } else {
                return "<span class='badge px-2 badge-light-warning rounded-top-0 border border-dashed border-accent border-top-0'>&nbsp;</span>";
            }
        } else {
            return "<span class='badge px-2 badge-light-warning rounded-top-0 border border-dashed border-secondary border-top-0 border-accent'>No attendance</span>";
        }
    }

    public function getFirstRowHtml($attendanceRecordsOfThisWorker, $date, $user)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        if ($user->hasRole('PSP')) {
            $naHtml = strtotime($date) <= strtotime(date('Y-m-d')) ? "<span class='badge badge-secondary bg-transparent px-2 border border-dashed border-accent'>N/A</span>" : "";

            return "<div class='d-flex flex-column gap-0'>
                     {$this->getStatusHtml($attendanceOfTheDay)}
                     {$this->getLateComingHtml($attendanceOfTheDay)}
                     {$this->getEarlyGoingHtml($attendanceOfTheDay)}
                 </div>";
        } else {
            return $attendanceOfTheDay && $attendanceOfTheDay?->shift_start
                ?
                Carbon::parse($attendanceOfTheDay?->shift_start)->format('h:i A')
                :
                "";
        }
    }

    public function getSecondRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        return $attendanceOfTheDay && $attendanceOfTheDay?->shift_end ? Carbon::parse($attendanceOfTheDay?->shift_end)->format('h:i A') : "";
    }

    public function getThirdRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        return $attendanceOfTheDay && $attendanceOfTheDay?->in_time_processed ? Carbon::parse($attendanceOfTheDay?->in_time_processed)->format('h:i A') : "";
    }

    public function getFourthRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        return $attendanceOfTheDay && $attendanceOfTheDay?->out_time_processed ? Carbon::parse($attendanceOfTheDay?->out_time_processed)->format('h:i A') : "";
    }

    public function getFifthRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;
        $textClass = $attendanceOfTheDay?->status == "M/P" ? "text-danger" : "";
        $status = $attendanceOfTheDay?->is_weekoff && in_array($attendanceOfTheDay?->status, ['P', 'H/D', 'A']) ? 'W/O' : $attendanceOfTheDay?->status; # Asked to modify by Santu
        #Added this line to show the Present in the datable only. -- 2025-02-23
        $status = $status == 'M/P' && $date == date('Y-m-d') ? 'P' :  $status;
        return $attendanceOfTheDay ? "<strong class='{$textClass}'>{$status}</strong>" : "";
    }

    public function getSixthRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        $status_remarks = $attendanceOfTheDay?->is_weekoff && in_array($attendanceOfTheDay?->status, ['P', 'H/D', 'A']) ? 'Week Off' : $attendanceOfTheDay?->status_remarks; # Asked to modify by Santu

        $statusRemarsHtml = '<div class="text-wrap mb-0 lh-sm " style="min-width: 100px; max-width: 200px;"><small style="font-size:0.75rem">' . $status_remarks . '</small></div>';
        return $attendanceOfTheDay ? $statusRemarsHtml : "";
    }

    public function getSeventhRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;
        $late_coming_minutes = $attendanceOfTheDay?->late_coming_minutes ?? 0;
        return $attendanceOfTheDay ? $late_coming_minutes : "";
    }

    public function getEightthRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;
        $early_going_minutes = $attendanceOfTheDay?->early_going_minutes ?? 0;
        return $attendanceOfTheDay ? $early_going_minutes : "";
    }

    public function getNinethRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;
        $grace_minutes = $attendanceOfTheDay?->grace_minutes ?? 0;
        return $attendanceOfTheDay ? $grace_minutes : "";
    }

    public function getTenthRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        return $attendanceOfTheDay && $attendanceOfTheDay?->in_time ? Carbon::parse($attendanceOfTheDay?->in_time)->format('h:i A') : "";
    }

    public function getEleventhRowHtml($attendanceRecordsOfThisWorker, $date)
    {
        $attendanceOfTheDay = $attendanceRecordsOfThisWorker ? $attendanceRecordsOfThisWorker->first(function ($item) use ($date) {
            return $item->date->toDateString() === $date;
        }) : null;

        return $attendanceOfTheDay && $attendanceOfTheDay?->out_time ? Carbon::parse($attendanceOfTheDay?->out_time)->format('h:i A') : "";
    }
}
