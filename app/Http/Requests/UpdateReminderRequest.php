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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],

            'remind_at' => ['sometimes', 'required', 'date'],

            'remindable_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'remindable_id' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}
