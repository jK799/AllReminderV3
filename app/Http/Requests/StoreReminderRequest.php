<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth:sanctum i tak pilnuje dostępu
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],

            // przypisanie: do device albo do vehicle albo ogólne (null/null)
            'device_id'    => ['nullable', 'integer'],
            'vehicle_id'   => ['nullable', 'integer'],

            'due_at'       => ['required', 'date'],
            'remind_at'    => ['nullable', 'date'],

            'is_active'    => ['sometimes', 'boolean'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}
