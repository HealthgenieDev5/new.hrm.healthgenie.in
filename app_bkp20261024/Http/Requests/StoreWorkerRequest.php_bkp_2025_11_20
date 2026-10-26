<?php

namespace App\Http\Requests;

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
            'mobile_number' => ['required', 'numeric', 'digits:10', 'unique:workers,mobile_number', Rule::unique('workers')],
            'gender' => ['required', 'in:male,female,other'],
            'adhar_number' => ['required', 'string', 'size:12', 'regex:/^[a-zA-Z0-9]{12}$/', Rule::unique('workers')],
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
            'mobile_number' => ['required', 'numeric', 'digits:10', 'unique:workers,mobile_number', Rule::unique('workers')],
            'gender' => ['required', 'in:male,female,other'],
            'adhar_number' => ['required', 'string', 'size:12', 'regex:/^[a-zA-Z0-9]{12}$/', Rule::unique('workers')],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_front' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'adhar_back' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
