<?php

namespace App\Http\Requests;

use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateWorkerRequest extends FormRequest
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

        // $worker = $this->route('worker'); // Get the worker from the route
        // return Gate::allows('update', $worker);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $worker = $this->route('worker');

        // Ensure worker is an instance of Worker model (not null or string)
        if (!$worker instanceof \App\Models\Worker) {
            abort(400, 'Invalid worker data'); // Handle error gracefully
        }

        if ($this->user->hasRole('PSP')) {
            return $this->pspRules($worker);
        }
        if ($this->user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
            return $this->adminHrRules($worker);
        }

        return []; // Fallback empty rules

    }

    /**
     * Validation rules for PSP role.
     */
    private function pspRules($worker): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'father_name' => ['required', 'string', 'min:2'],
            'shift_id' => 'required|exists:shifts,id',
            'mobile_number' => $this->mobileNumberRules($worker),
            'gender' => ['required', 'in:male,female,other'],
            'adhar_number' => $this->adharNumberRules($worker),
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_front' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:2048'],
            'adhar_back' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:2048'],
        ];
    }

    /**
     * Validation rules for Super Admin and HR.
     */
    private function adminHrRules($worker): array
    {
        return [
            'code' => ['required', 'string', 'min:3', Rule::unique('workers')->ignore($worker->id)],
            'name' => ['required', 'string', 'min:2'],
            'father_name' => ['required', 'string', 'min:2'],
            'date_of_joining' => ['required', 'date'],
            'shift_id' => 'required|exists:shifts,id',
            'employment_status' => ['required', Rule::in(['Active', 'Left', 'Terminated', 'Absconded', 'Retired', 'Under Resignation'])],
            'date_of_leaving' => ['nullable', 'date', 'required_unless:employment_status,Active'],

            'mobile_number' => $this->mobileNumberRules($worker),
            'gender' => ['required', 'in:male,female,other'],
            'adhar_number' => $this->adharNumberRules($worker),
            'psp_id' => 'required|exists:psps,id',
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_front' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_back' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
    private function mobileNumberRules($worker): array
    {
        return [
            'required',
            'numeric',
            'digits:10',
            function ($attribute, $value, $fail) use ($worker) {
                // Get PSP ID based on user role
                $pspId = $this->user->hasRole('PSP')
                    ? $this->user->psp_id
                    : $this->input('psp_id');

                // Check if mobile number is used by an Active worker in ANY PSP (excluding current worker)
                $activeWorkerExists = Worker::where('mobile_number', $value)
                    ->where('employment_status', 'Active')
                    ->where('id', '!=', $worker->id)
                    ->exists();

                if ($activeWorkerExists) {
                    $fail('This mobile number is already assigned to an active worker.');
                }

                // Check if mobile number is used by a Left/inactive worker in the SAME PSP (excluding current worker)
                $leftWorkerInSamePsp = Worker::where('mobile_number', $value)
                    ->where('psp_id', $pspId)
                    ->where('employment_status', '!=', 'Active')
                    ->where('id', '!=', $worker->id)
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
    private function adharNumberRules($worker): array
    {
        return [
            'required',
            'string',
            'size:12',
            'regex:/^[a-zA-Z0-9]{12}$/',
            function ($attribute, $value, $fail) use ($worker) {
                // Get PSP ID based on user role
                $pspId = $this->user->hasRole('PSP')
                    ? $this->user->psp_id
                    : $this->input('psp_id');

                // Check if Adhar number is used by an Active worker in ANY PSP (excluding current worker)
                $activeWorkerExists = Worker::where('adhar_number', $value)
                    ->where('employment_status', 'Active')
                    ->where('id', '!=', $worker->id)
                    ->exists();

                if ($activeWorkerExists) {
                    $fail('This Adhar number is already assigned to an active worker.');
                }

                // Check if Adhar number is used by a Left/inactive worker in the SAME PSP (excluding current worker)
                $leftWorkerInSamePsp = Worker::where('adhar_number', $value)
                    ->where('psp_id', $pspId)
                    ->where('employment_status', '!=', 'Active')
                    ->where('id', '!=', $worker->id)
                    ->exists();

                if ($leftWorkerInSamePsp) {
                    $fail('This Adhar number was previously used by a worker in this PSP. Please use a different Adhar number.');
                }
            },
        ];
    }
}
