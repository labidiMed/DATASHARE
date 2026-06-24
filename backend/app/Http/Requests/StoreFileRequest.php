<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:1048576'], // 1 Go en Ko
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:7'],
            'password' => ['nullable', 'string', 'min:6'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:30'],
        ];
    }
}
