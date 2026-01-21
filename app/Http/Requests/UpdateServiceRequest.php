<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id'       => ['nullable', 'integer', 'exists:devices,id'],
            'vehicle_id'      => ['nullable', 'integer', 'exists:vehicles,id'],

            'title'           => ['sometimes', 'required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],

            'last_done_at'    => ['nullable', 'date'],
            'next_due_at'     => ['nullable', 'date'],

            'interval_value'  => ['nullable', 'integer', 'min:1'],
            'interval_unit'   => ['nullable', 'string', 'max:50'],

            'is_active'       => ['sometimes', 'boolean'],
        ];
    }
}
