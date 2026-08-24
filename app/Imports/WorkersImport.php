<?php

namespace App\Imports;

use App\Models\Psp;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class WorkersImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',', // Specify the CSV delimiter (default is comma)
        ];
    }

    public function model(array $row)
    {

        // Process date fields safely
        $dateOfBirth = $this->parseDate($row['dob']);
        $dateOfJoining = $this->parseDate($row['doj']);


        // Find or create the psp
        $psp = Psp::firstOrCreate(
            [
                'name' => $row['psp_name'],
                'code' => $row['psp_code']
            ],
            [
                'address' => 'Unknown',
                'city' => 'Unknown',
                'state' => 'Unknown',
                'pincode' => '000000',
                'mobile_number' => '0000000000',
                'email_id' => 'default@example.com',
                'contact_person' => 'Unknown',
            ]
        );

        // Create or update the worker
        return new Worker([
            'code' => $row['emp_code'],
            'name' => $row['name'],
            'father_name' => $row['father_s_name'],
            'date_of_birth' => $dateOfBirth,
            'date_of_joining' => $dateOfJoining,
            'adhar_number' => $row['aadhar_no'],
            'mobile_number' => $row['mobile_no'],
            'esic_number' => $row['esic_no'],
            'gender' => $row['gender'],
            'marital_status' => $row['marital_status'],
            'permanent_address' => $row['permanent_address'],
            'present_address' => $row['present_address'],
            'employment_status' => $row['status'],
            'psp_id' => $psp->id,
        ]);
    }

    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
