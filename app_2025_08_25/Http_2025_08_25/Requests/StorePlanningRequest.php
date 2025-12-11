<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // return [
        //     'line_id' => 'required|exists:lines,id',
        //     'shift_type' => 'required|in:day,night',
        //     // 'date' => 'required|date',
        //     'workers' => 'required|integer|min:0',
        //     'extras' => 'nullable|integer|min:0',
        //     'supervisors' => 'nullable|integer|min:0',
        //     'qas' => 'nullable|integer|min:0',
        //     'services' => 'nullable|integer|min:0',
        // ];

        return [
            'line_id' => 'required|exists:lines,id',
            // 'date' => 'required|date',

            // Day Shift
            'day.workers' => 'required|integer|min:0',
            'day.extras' => 'nullable|integer|min:0',
            'day.supervisors' => 'nullable|integer|min:0',
            'day.qas' => 'nullable|integer|min:0',
            'day.services' => 'nullable|integer|min:0',
            'day.movers' => 'nullable|integer|min:0',

            // Night Shift
            'night.workers' => 'required|integer|min:0',
            'night.extras' => 'nullable|integer|min:0',
            'night.supervisors' => 'nullable|integer|min:0',
            'night.qas' => 'nullable|integer|min:0',
            'night.services' => 'nullable|integer|min:0',
            'night.movers' => 'nullable|integer|min:0',
        ];
    }
}
