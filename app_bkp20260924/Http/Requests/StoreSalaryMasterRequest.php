<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSalaryMasterRequest extends FormRequest
{
    public $user;

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
        return $this->user->hasPermission('Create Salary Master');        
        // return true; // Set to true if all users can access it
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'worker_id' => 'required|unique:salary_masters|exists:workers,id',
            'basic' => 'required|numeric|min:0',
            'hra' => 'required|numeric|min:0',
            'conveyance' => 'required|numeric|min:0',
            'medical_allowance' => 'required|numeric|min:0',
            'special_allowance' => 'required|numeric|min:0',
            'fuel_allowance' => 'required|numeric|min:0',
            'vacation_allowance' => 'required|numeric|min:0',
            'other_allowance' => 'required|numeric|min:0',
            'bonus_allowed' => 'required|boolean',
            'pf_allowed' => 'required|boolean',
            'esic_allowed' => 'required|boolean',
        ];
    }
}
