<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistItemResource extends JsonResource
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
            'content' => $this->content,
            'is_completed' => $this->is_completed,
            'position' => $this->position,
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'completed_by' => $this->whenLoaded('completedBy', fn() => [
                'id' => $this->completedBy->id,
                'name' => $this->completedBy->name
            ])
        ];
    }
}
