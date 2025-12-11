<?php

namespace App\Services\AttendanceApi;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttendanceApiMachineThree
{

    protected $url;
    protected $auth;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->url = config('services.machine_3_api.url');
        $corporateId = config('services.machine_3_api.corporate_id');
        $username = config('services.machine_3_api.username');
        $password = config('services.machine_3_api.password');


        $this->auth = base64_encode("{$corporateId}:{$username}:{$password}:true");
    }

    public function storeAttendance($empcode, $fromDate, $toDate)
    {
        $postData = ['Empcode' => $empcode, 'FromDate' => $fromDate, 'ToDate' => $toDate];

        try {
            $response = Http::withHeaders(['Authorization' => "Basic {$this->auth}"])->get($this->url, $postData);

            if ($response->successful()) {
                $data = $response->json();
                if ($data) {
                    foreach ($data['InOutPunchData'] ?? [] as $record) {

                        $intime = $record['INTime'] !== '--:--' ? $record['INTime'] : null;
                        $outtime = $record['OUTTime'] !== '--:--' ? $record['OUTTime'] : null;
                        if (!empty($intime) && !empty($outtime) && strtotime($intime) > strtotime($outtime)) {
                            $shiftType = 'night';
                        } else {
                            $shiftType = 'day';
                        }

                        Attendance::updateOrCreate(
                            [
                                'empcode' => $record['Empcode'],
                                'date' => Carbon::createFromFormat('d/m/Y', $record['DateString'])->format('Y-m-d'),
                                // 'shift_type' => 'night',
                                'shift_type' => $shiftType,
                            ],
                            [
                                'intime' => $record['INTime'] !== '--:--' ? $record['INTime'] : null,
                                'outtime' => $record['OUTTime'] !== '--:--' ? $record['OUTTime'] : null,
                                'work_time' => $record['WorkTime'],
                                'over_time' => $record['OverTime'],
                                'break_time' => $record['BreakTime'],
                                'status' => $record['Status'],
                                'remark' => $record['Remark'],
                                'early_out' => $record['Erl_Out'],
                                'late_in' => $record['Late_In'],
                                'name' => $record['Name'],
                            ]
                        );
                    }
                }
                Log::info('Machine 3 Attendance fetched and stored successfully.');
                return ['success' => true, 'message' => 'Machine 3 Attendance fetched and stored successfully.'];
            } else {
                Log::error('Machine 3 Attendance Failed to fetch: ' . $response->status());
                return ['success' => false, 'message' => 'Machine 3 Attendance Failed to fetch: ' . $response->status()];
            }
        } catch (\Exception $e) {
            Log::error('Machine 3 Attendance Error while fetching: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Machine 3 Attendance Error while fetching: ' . $e->getMessage()];
        }
    }
}
