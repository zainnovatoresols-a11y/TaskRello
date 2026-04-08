<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Board member check is handled in CommentController
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => [
                'required',
                'string',
                'min:1',   // no empty comments
                'max:5000',
            ],
        ];
    }

    // Strip whitespace from the start/end of comment body
    protected function prepareForValidation(): void
    {
        $this->merge([
            'body' => trim($this->body ?? ''),
        ]);
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Comment cannot be empty.',
            'body.max'      => 'Comment is too long (max 5000 characters).',
        ];
    }
}
