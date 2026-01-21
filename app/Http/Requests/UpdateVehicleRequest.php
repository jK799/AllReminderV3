<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'required', 'string', 'max:255'],
            'description'  => ['sometimes', 'nullable', 'string'],

            'device_id'    => ['sometimes', 'nullable', 'integer'],
            'vehicle_id'   => ['sometimes', 'nullable', 'integer'],

            'due_at'       => ['sometimes', 'required', 'date'],
            'remind_at'    => ['sometimes', 'nullable', 'date'],

            'is_active'    => ['sometimes', 'boolean'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
