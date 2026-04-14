<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('card'));
    }

    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'due_date' => [
                'sometimes',
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            'cover_color' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'is_archived' => [
                'sometimes',
                'boolean',
            ],
            'list_id' => [
                'sometimes',
                'exists:lists,id',
            ],
            'position' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}
