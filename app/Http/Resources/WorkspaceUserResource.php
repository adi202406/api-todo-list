<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceUserResource extends JsonResource
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
            'user_id' => $this->user_id,
            'workspace_id' => $this->workspace_id,
            'role' => $this->role,
            'status' => $this->status,
            'invited_by' => $this->invited_by,
            'joined_at' => $this->joined_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'workspace' => new WorkspaceResource($this->whenLoaded('workspace')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
