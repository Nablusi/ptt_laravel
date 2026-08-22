<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChannelMember;
use App\Models\Channel;
use App\Models\User;
class RemoveUserFromChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        $channel = Channel::find($this->channel_id);
        //حراء هاد من اليوزر 
        $targetUser = User::find($this->user_id);
        return $channel && $targetUser && $this->user()->can('remove', [$channel, $targetUser]);
    }

    public function rules(): array
    {
        return [
            'channel_id' => "required|exists:channels,id",
            'user_id' => "required|exists:users,id",
        ];
    }

    public function messages(): array
    {
        return [
            'channel_id.required' => 'channel_id is required',
            'channel_id.exists' => 'Channel not found',
            'user_id.required' => 'user_id is required',
            'user_id.exists' => 'User not found',
        ];
    }
}