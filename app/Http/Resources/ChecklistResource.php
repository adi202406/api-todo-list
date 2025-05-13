<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'position'     => $this->position,
            'is_completed' => $this->is_completed,
            'progress'     => [
                'completed' => $this->completed_items,
                'total'     => $this->total_items,
            ],
            'created_at'   => $this->created_at->toDateTimeString(),
            'items'        => ChecklistItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
