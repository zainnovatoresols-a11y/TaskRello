<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'due_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'cover_color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'position' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'      => 'Card title cannot be empty.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }
}
