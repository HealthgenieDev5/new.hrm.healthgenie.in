<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class AttendanceCacheHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public static function clearFilterCache()
    {
        Cache::forget('attendance_filter_data');
    }
}
