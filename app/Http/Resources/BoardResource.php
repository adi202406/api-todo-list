<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
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
            'workspace_id' => $this->workspace_id,
            'name' => $this->name,
            'position' => $this->position,
            'color' => $this->color,
            'is_favorite' => $this->is_favorite,
            'stats' => [
                'cards_count' => $this->whenCounted('cards'),
                'completed_cards' => $this->cards->where('is_completed', true)->count()
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'workspace' => $this->whenLoaded('workspace', fn() => [
                'id' => $this->workspace->id,
                'name' => $this->workspace->name
            ])
        ];
    }
}
