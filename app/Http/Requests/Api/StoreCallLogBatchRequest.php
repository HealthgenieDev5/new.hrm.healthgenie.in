<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallLogBatchRequest extends FormRequest
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
        return [
            'device_id' => ['required', 'string', 'max:255'],
            'device' => ['nullable', 'array'],
            'device.brand' => ['nullable', 'string', 'max:255'],
            'device.model' => ['nullable', 'string', 'max:255'],
            'device.manufacturer' => ['nullable', 'string', 'max:255'],
            'device.android_version' => ['nullable', 'string', 'max:50'],
            'sent_at' => ['required', 'date'],
            'calls' => ['required', 'array'],
            'calls.*.number' => ['required', 'string', 'max:50'],
            'calls.*.type' => ['required', 'string', 'max:50'],
            'calls.*.duration' => ['required', 'integer', 'min:0'],
            'calls.*.timestamp' => ['required', 'date'],
        ];
    }
}
