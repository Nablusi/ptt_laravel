<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'channel_id' => $this->channel_id,
            'channel_name' => $this->whenLoaded('channel', fn () => $this->channel?->name),
            'channel_role' => $this->channel_role,
            'level' => $this->whenLoaded('channel', fn () => $this->channel?->level),
        ];
    }
}