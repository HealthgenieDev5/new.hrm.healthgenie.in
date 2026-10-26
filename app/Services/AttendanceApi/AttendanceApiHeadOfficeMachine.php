<?php

namespace App\Services\AttendanceApi;

use App\Models\Attendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttendanceApiHeadOfficeMachine
{

    protected $url;
    protected $auth;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->url = config('services.head_office_machine_api.url');
        $corporateId = config('services.head_office_machine_api.corporate_id');
        $username = config('services.head_office_machine_api.username');
        $password = config('services.head_office_machine_api.password');

        $this->auth = base64_encode("{$corporateId}:{$username}:{$password}:true");
    }

    public function storeAttendance($empcode, $fromDate, $toDate)
    {
        $postData = ['Empcode' => $empcode, 'FromDate' => $fromDate, 'ToDate' => $toDate];

        // dd($postData);

        try {
            $response = Http::withHeaders(['Authorization' => "Basic {$this->auth}"])->get($this->url, $postData);

            if ($response->successful()) {
                $data = $response->json();

                if ($data) {

                    //filter out any empcode which does not exist in our system. 
                    //keep only those who are in our system.
                    //basically an intersection of employee codes
                    $records = $data['InOutPunchData'] ?? [];

                    // $totalRecords = count($records);

                    $existingCodes = Worker::whereIn('code', array_unique(array_column($records, 'Empcode')))
                        ->pluck('code')
                        ->flip();

                    $records = array_filter($records, fn($record) => isset($existingCodes[$record['Empcode'] ?? null]));

                    // $filteredRecords = count($records);
                    // dd(['totalRecords' =>$totalRecords, 'filteredRecords'=>$filteredRecords]);

                    foreach ($records as $record) {

                        $shiftType = 'day';
                        $intime = $record['INTime'] !== '--:--' ? $record['INTime'] : null;
                        $outtime = $record['OUTTime'] !== '--:--' ? $record['OUTTime'] : null;
                        if ($intime || $outtime) {
                            Attendance::updateOrCreate(
                                [
                                    'empcode' => $record['Empcode'],
                                    'date' => Carbon::createFromFormat('d/m/Y', $record['DateString'])->format('Y-m-d'),
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
                }
                Log::info('Head Office Machine Attendance fetched and stored successfully.');
                return ['success' => true, 'message' => 'Attendance fetched and stored successfully.'];
            } else {
                Log::error('Head Office Machine Attendance Failed to fetch: ' . $response->status());
                return ['success' => false, 'message' => 'Failed to fetch attendance: ' . $response->status()];
            }
        } catch (\Exception $e) {
            Log::error('Head Office Machine Attendance Error while fetching: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Head Office Machine Attendance Error while fetching: ' . $e->getMessage()];
        }
    }
}
