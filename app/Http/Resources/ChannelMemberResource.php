<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'channel_id' => $this->channel_id,
            'user_id' => $this->user_id,
            'channel_role' => $this->channel_role,
            'is_muted' => (bool) $this->is_muted,
            'joined_at' => $this->joined_at,
            'username' => $this->whenLoaded('user', fn () => $this->user?->username),
            'status' => $this->whenLoaded('user', fn () => $this->user?->status),
        ];
    }
}