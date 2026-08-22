<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ChannelMember;
use App\Models\Channel;
use App\Models\User;    
class AssignUserToChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        // user not exist حراء
        $channel = Channel::find($this->channel_id);
        $targetUser = User::find($this->user_id);
    
        return $channel
            && $targetUser
            && $this->user()->can('assign', [$channel, $targetUser]);
    }

    public function rules(): array
    {
        return [
            'channel_id' => "required|exists:channels,id",
            'user_id' => "required|exists:users,id",
            'channel_role' => "nullable|in:company_admin,channel_admin,member",
        ];
    }

    public function messages(): array
    {
        return [
            'channel_id.required' => 'channel_id is required',
            'channel_id.exists' => 'Channel not found',
            'user_id.required' => 'user_id is required',
            'user_id.exists' => 'User not found',
            'channel_role.in' => 'channel_role must be company_admin, channel_admin or member',
        ];
    }
}