<?php

namespace App\Services\AttendanceApi;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttendanceApiMachineOne
{

    protected $url;
    protected $auth;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->url = config('services.machine_1_api.url');
        $corporateId = config('services.machine_1_api.corporate_id');
        $username = config('services.machine_1_api.username');
        $password = config('services.machine_1_api.password');

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
                    // foreach ($data['InOutPunchData'] ?? [] as $record) {

                    //     $shiftType = 'day';

                    //     Attendance::updateOrCreate(
                    //         [
                    //             'empcode' => $record['Empcode'],
                    //             'date' => Carbon::createFromFormat('d/m/Y', $record['DateString'])->format('Y-m-d'),
                    //             'shift_type' => $shiftType,
                    //         ],
                    //         [
                    //             'intime' => $record['INTime'] !== '--:--' ? $record['INTime'] : null,
                    //             'outtime' => $record['OUTTime'] !== '--:--' ? $record['OUTTime'] : null,
                    //             'work_time' => $record['WorkTime'],
                    //             'over_time' => $record['OverTime'],
                    //             'break_time' => $record['BreakTime'],
                    //             'status' => $record['Status'],
                    //             'remark' => $record['Remark'],
                    //             'early_out' => $record['Erl_Out'],
                    //             'late_in' => $record['Late_In'],
                    //             'name' => $record['Name'],
                    //         ]
                    //     );
                    // }

                    $attendanceRecords = [];

                    foreach ($data['InOutPunchData'] ?? [] as $record) {
                        $shiftType = 'day';
                        
                        $attendanceRecord = [
                            'empcode' => $record['Empcode'],
                            'date' => Carbon::createFromFormat('d/m/Y', $record['DateString'])->format('Y-m-d'),
                            'shift_type' => $shiftType,
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
                        ];

                        // if( $attendanceRecord['date'] == '2025-12-01' ){
                        //     dd($attendanceRecord);
                        // }

                        $attendanceRecords[] = $attendanceRecord;
                    }

                    if (!empty($attendanceRecords)) {
                        $totalRecords = count($attendanceRecords);
                        $batchSize = 100;
                        $processed = 0;

                        $batches = array_chunk($attendanceRecords, $batchSize);

                        foreach ($batches as $batch) {
                            // dd($batch);
                            Attendance::upsert(
                                $batch,
                                ['empcode', 'date', 'shift_type'],
                                ['intime', 'outtime', 'work_time', 'over_time', 'break_time', 'status', 'remark', 'early_out', 'late_in', 'name']
                            );

                            $processed += count($batch);
                            echo "{$processed} of {$totalRecords} rows has been updated\n";
                            Log::info("Machine 1: {$processed} of {$totalRecords} rows has been updated");
                        }
                    }


                }
                Log::info('Machine 1 Attendance fetched and stored successfully.');
                return ['success' => true, 'message' => 'Attendance fetched and stored successfully.'];
            } else {
                Log::error('Machine 1 Attendance Failed to fetch: ' . $response->status());
                return ['success' => false, 'message' => 'Failed to fetch attendance: ' . $response->status()];
            }
        } catch (\Exception $e) {
            Log::error('Machine 1 Attendance Error while fetching: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Machine 1 Attendance Error while fetching: ' . $e->getMessage()];
        }
    }
}
