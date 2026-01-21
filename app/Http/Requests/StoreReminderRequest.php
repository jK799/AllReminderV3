<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // kluczowy field:
            'remind_at' => ['required', 'date'],

            // opcjonalne przypisanie (polimorficzne) – możesz na razie nie wysyłać
            'remindable_type' => ['nullable', 'string', 'max:255'],
            'remindable_id' => ['nullable', 'integer'],
        ];
    }
}
