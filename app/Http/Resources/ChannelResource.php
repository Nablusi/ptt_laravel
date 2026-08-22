<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'parent_channel_id' => $this->parent_channel_id,
            'name' => $this->name,
            'level' => $this->level,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at,
            'company_name' => $this->whenLoaded('company', fn () => $this->company?->name),
            'parent_channel_name' => $this->whenLoaded('parent', fn () => $this->parent?->name),

        ];
    }
}
