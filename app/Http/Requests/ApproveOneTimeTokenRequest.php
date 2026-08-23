<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApproveOneTimeTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string|max:64',
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'The token is required',
            'token.string' => 'The token must be a string',
            'token.max' => 'The token must be less than 64 characters',
        ];
    }
}
