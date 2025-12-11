<?php

namespace App\Observers;

use App\Helpers\AttendanceCacheHelper;

class AttendanceFilterObserver
{
    public function created($model)
    {
        AttendanceCacheHelper::clearFilterCache();
    }

    public function updated($model)
    {
        AttendanceCacheHelper::clearFilterCache();
    }

    public function deleted($model)
    {
        AttendanceCacheHelper::clearFilterCache();
    }
}
