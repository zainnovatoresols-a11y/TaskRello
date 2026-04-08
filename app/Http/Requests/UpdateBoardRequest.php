<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoardRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Policy check: only board members can update
        return $this->user()->can('update', $this->route('board'));
    }

    public function rules(): array
    {
        return [
            // 'sometimes' = only validate if this field is present
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
            'background_color' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'is_archived' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
