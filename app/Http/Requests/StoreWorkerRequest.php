<?php

namespace App\Http\Requests;

use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreWorkerRequest extends FormRequest
{

    private $user;

    /**
     * Constructor to assign the authenticated user.
     */
    public function __construct()
    {
        parent::__construct(); // Ensure the parent constructor runs
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive', 'PSP']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->user->hasRole('PSP')) {
            return $this->pspRules();
        }
        if ($this->user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
            return $this->adminHrRules();
        }

        return []; // Fallback empty rules

    }

    /**
     * Validation rules for PSP role.
     */
    private function pspRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'father_name' => ['required', 'string', 'min:2'],
            'shift_id' => 'required|exists:shifts,id',
            'mobile_number' => $this->mobileNumberRules(),
            'gender' => ['required', 'in:male,female,other'],
            'adhar_number' => $this->adharNumberRules(),
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_front' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_back' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Validation rules for Super Admin and HR.
     */
    private function adminHrRules(): array
    {
        return [
            'psp_id' => 'required|exists:psps,id',
            'name' => ['required', 'string', 'min:2'],
            'date_of_joining' => ['required', 'date'],
            'father_name' => ['required', 'string', 'min:2'],
            'shift_id' => 'required|exists:shifts,id',
            'mobile_number' => $this->mobileNumberRules(),
            'gender' => ['required', 'in:male,female,other'],
            'adhar_number' => $this->adharNumberRules(),
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_front' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_back' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Mobile number validation rules - unique per PSP for Active workers only.
     * Prevents: Active worker in ANY PSP, Left worker in SAME PSP
     * Allows: Left worker in DIFFERENT PSP
     *
     * Scenario 1: Worker with mobile 9876543210 is Active in PSP-A
     * - ❌ Cannot add new worker with same mobile in PSP-A
     * - ❌ Cannot add new worker with same mobile in PSP-B (or any PSP)
     *
     * Scenario 2: Worker with mobile 9876543210 has Left from PSP-A
     * - ❌ Cannot add new worker with same mobile in PSP-A
     * - ✅ Can add new worker with same mobile in PSP-B (different PSP)
     *
     * Scenario 3: Worker with mobile 9876543210 has Left from PSP-A and is Active in PSP-B
     * - ❌ Cannot add new worker with same mobile in any PSP (because Active in PSP-B)
     */
    private function mobileNumberRules(): array
    {
        return [
            'required',
            'numeric',
            'digits:10',
            function ($attribute, $value, $fail) {
                // Get PSP ID based on user role
                $pspId = $this->user->hasRole('PSP')
                    ? $this->user->psp_id
                    : $this->input('psp_id');

                // Check if mobile number is used by an Active worker in ANY PSP
                $activeWorkerExists = Worker::where('mobile_number', $value)
                    ->where('employment_status', 'Active')
                    ->exists();

                if ($activeWorkerExists) {
                    $fail('This mobile number is already assigned to an active worker.');
                }

                // Check if mobile number is used by a Left/inactive worker in the SAME PSP
                $leftWorkerInSamePsp = Worker::where('mobile_number', $value)
                    ->where('psp_id', $pspId)
                    ->where('employment_status', '!=', 'Active')
                    ->exists();

                if ($leftWorkerInSamePsp) {
                    $fail('This mobile number was previously used by a worker in this PSP. Please use a different mobile number.');
                }
            },
        ];
    }

    /**
     * Adhar number validation rules - unique per PSP for Active workers only.
     * Prevents: Active worker in ANY PSP, Left worker in SAME PSP
     * Allows: Left worker in DIFFERENT PSP
     *
     * Scenario 1: Adhar 123456789012 is Active in PSP-A
     * - ❌ Cannot add worker with same Adhar in PSP-A
     * - ❌ Cannot add worker with same Adhar in PSP-B (or any PSP)
     *
     * Scenario 2: Adhar 123456789012 has Left from PSP-A
     * - ❌ Cannot add worker with same Adhar in PSP-A
     * - ✅ Can add worker with same Adhar in PSP-B (different PSP)
     *
     * Scenario 3: Adhar 123456789012 has Left from PSP-A and is Active in PSP-B
     * - ❌ Cannot add worker with same Adhar in any PSP (because Active in PSP-B
     */
    private function adharNumberRules(): array
    {
        return [
            'required',
            'string',
            'size:12',
            'regex:/^[a-zA-Z0-9]{12}$/',
            function ($attribute, $value, $fail) {
                // Get PSP ID based on user role
                $pspId = $this->user->hasRole('PSP')
                    ? $this->user->psp_id
                    : $this->input('psp_id');

                // Check if Adhar number is used by an Active worker in ANY PSP
                $activeWorkerExists = Worker::where('adhar_number', $value)
                    ->where('employment_status', 'Active')
                    ->exists();

                if ($activeWorkerExists) {
                    $fail('This Adhar number is already assigned to an active worker.');
                }

                // Check if Adhar number is used by a Left/inactive worker in the SAME PSP
                $leftWorkerInSamePsp = Worker::where('adhar_number', $value)
                    ->where('psp_id', $pspId)
                    ->where('employment_status', '!=', 'Active')
                    ->exists();

                if ($leftWorkerInSamePsp) {
                    $fail('This Adhar number was previously used by a worker in this PSP. Please use a different Adhar number.');
                }
            },
        ];
    }
}
