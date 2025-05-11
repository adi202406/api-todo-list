<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'banner_image' => $this->banner_image,
            'visibility' => $this->visibility,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'members_count' => $this->users()->count(), // Add this line
            'members' => WorkspaceUserResource::collection($this->whenLoaded('workspaceUsers')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
