<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Channel;
class StoreSubChannelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('createSub', Channel::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'parent_channel_id' => 'required|exists:channels,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'parent_channel_id.required' => 'The parent channel field is required.',
            'parent_channel_id.exists' => 'The selected parent channel is invalid.',
        ];
    }
}
