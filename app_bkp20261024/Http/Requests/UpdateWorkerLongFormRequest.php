<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateWorkerLongFormRequest extends FormRequest
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
        $worker = $this->route('worker');

        // Ensure worker is an instance of Worker model (not null or string)
        if (!$worker instanceof \App\Models\Worker) {
            abort(400, 'Invalid worker data'); // Handle error gracefully
        }



        return [
            'husband_name' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d'],
            'religion' => ['nullable', 'in:Hindu,Muslim,Christian,Sikh,Jain,Budhist'],
            'marital_status' => ['nullable', 'in:Unmarried,Married,Divorced,Widowed'],
            'permanent_district' => ['nullable', 'string'],
            'permanent_state' => ['nullable', 'string'],
            'permanent_pincode' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'present_district' => ['nullable', 'string'],
            'present_state' => ['nullable', 'string'],
            'present_pincode' => ['nullable', 'string'],
            'present_address' => ['nullable', 'string'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'workertype_id' => ['nullable', 'exists:workertypes,id'],
            'working_area_id' => ['nullable', 'exists:working_areas,id'],
            'production_product_id' => ['nullable', 'exists:production_products,id'],
            'level_id' => ['nullable', 'exists:levels,id'],
            'uan_number' => ['nullable', 'string'],
            'uan_photo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'esic_number' => $this->esicNumberRules($worker),
            'division_id' => ['nullable', 'exists:divisions,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'subdepartment_id' => ['nullable', 'exists:subdepartments,id'],
            'line_id' => ['nullable', 'exists:lines,id'],
            'process_id' => ['nullable', 'exists:processes,id'],
            'form_11_photo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'affidavit_photo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'lop_photo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'husband_name.string' => 'The husband name must be a valid string.',

            'date_of_birth.date_format' => 'The date of birth must be in the format yyyy-mm-dd.',

            'religion.in' => 'The selected religion is invalid. Allowed religions: Hindu, Muslim, Christian, Sikh, Jain, Budhist.',

            'marital_status.in' => 'The selected marital status is invalid. Allowed statuses: Unmarried, Married, Divorced, Widowed.',

            'permanent_district.string' => 'The permanent district must be a valid string.',

            'permanent_state.string' => 'The permanent state must be a valid string.',

            'permanent_pincode.string' => 'The permanent pincode must be a valid string.',

            'permanent_address.string' => 'The permanent address must be a valid string.',

            'present_district.string' => 'The present district must be a valid string.',

            'present_state.string' => 'The present state must be a valid string.',

            'present_pincode.string' => 'The present pincode must be a valid string.',

            'present_address.string' => 'The present address must be a valid string.',

            'designation_id.exists' => 'The selected designation does not exist.',

            'workertype_id.exists' => 'The selected worker type does not exist.',

            'workingarea_id.exists' => 'The selected working area does not exist.',

            'production_product_id.exists' => 'The selected production product does not exist.',

            'level_id.exists' => 'The selected level does not exist.',

            'uan_number.string' => 'The UAN number must be a valid string.',

            'uan_photo.file' => 'The UAN photo must be a file.',
            'uan_photo.mimes' => 'The UAN photo must be a file of type: jpeg, png, jpg, gif, or pdf.',
            'uan_photo.max' => 'The UAN photo must not be greater than 5 MB.',

            'esic_number.string' => 'The ESIC number must be a valid string.',

            'division_id.exists' => 'The selected division does not exist.',

            'department_id.exists' => 'The selected department does not exist.',

            'subdepartment_id.exists' => 'The selected subdepartment does not exist.',

            'line_id.exists' => 'The selected line does not exist.',

            'process_id.exists' => 'The selected process does not exist.',

            'form_11_photo.file' => 'The Form 11 photo must be a file.',
            'form_11_photo.mimes' => 'The Form 11 photo must be a file of type: jpeg, png, jpg, gif, or pdf.',
            'form_11_photo.max' => 'The Form 11 photo must not be greater than 5 MB.',

            'affidavit_photo.file' => 'The affidavit photo must be a file.',
            'affidavit_photo.mimes' => 'The affidavit photo must be a file of type: jpeg, png, jpg, gif, or pdf.',
            'affidavit_photo.max' => 'The affidavit photo must not be greater than 5 MB.',

            'lop_photo.file' => 'The LOP photo must be a file.',
            'lop_photo.mimes' => 'The LOP photo must be a file of type: jpeg, png, jpg, gif, or pdf.',
            'lop_photo.max' => 'The LOP photo must not be greater than 5 MB.',
        ];
    }

    /**
     * ESIC number validation rules - unique per PSP for Active workers only.
     * Prevents: Active worker in ANY PSP, Left worker in SAME PSP
     * Allows: Left worker in DIFFERENT PSP
     *
     * Scenario 1: ESIC number 1234567890 is Active in PSP-A
     * - ❌ Cannot add worker with same ESIC in PSP-A
     * - ❌ Cannot add worker with same ESIC in PSP-B (or any PSP)
     *
     * Scenario 2: ESIC number 1234567890 has Left from PSP-A
     * - ❌ Cannot add worker with same ESIC in PSP-A
     * - ✅ Can add worker with same ESIC in PSP-B (different PSP)
     *
     * Scenario 3: ESIC number 1234567890 has Left from PSP-A and is Active in PSP-B
     * - ❌ Cannot add worker with same ESIC in any PSP (because Active in PSP-B)
     */
    private function esicNumberRules($worker): array
    {
        return [
            'nullable',
            'string',
            function ($attribute, $value, $fail) use ($worker) {
                // Skip validation if esic_number is empty
                if (empty($value)) {
                    return;
                }

                // Get PSP ID based on user role
                $pspId = $this->user->hasRole('PSP')
                    ? $this->user->psp_id
                    : $this->input('psp_id');

                // Check if ESIC number is used by an Active worker in ANY PSP (excluding current worker)
                $activeWorkerExists = \App\Models\Worker::where('esic_number', $value)
                    ->where('employment_status', 'Active')
                    ->where('id', '!=', $worker->id)
                    ->exists();

                if ($activeWorkerExists) {
                    $fail('This ESIC number is already assigned to an active worker.');
                }

                // Check if ESIC number is used by a Left/inactive worker in the SAME PSP (excluding current worker)
                $leftWorkerInSamePsp = \App\Models\Worker::where('esic_number', $value)
                    ->where('psp_id', $pspId)
                    ->where('employment_status', '!=', 'Active')
                    ->where('id', '!=', $worker->id)
                    ->exists();

                if ($leftWorkerInSamePsp) {
                    $fail('This ESIC number was previously used by a worker in this PSP. Please use a different ESIC number.');
                }
            },
        ];
    }
}
