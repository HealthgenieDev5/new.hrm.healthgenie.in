<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateWorkingAreaRequest extends FormRequest
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
        return $this->user->hasPermission('Manage Working Area');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workingArea = $this->route('working_area');
        if (!$workingArea instanceof \App\Models\WorkingArea) {
            abort(400, 'Invalid workingArea data'); // Handle error gracefully
        }

        return [
            'name' => ['required', 'string', 'min:2', Rule::unique('working_areas')->ignore($workingArea->id)],
        ];
    }
}
