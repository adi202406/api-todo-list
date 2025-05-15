<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  public function toArray($request)
    {
        return [
            'id' => $this->id,
            'card_id' => $this->card_id,
            'remind_at' => $this->remind_at->toIso8601String(),
            'channel' => $this->channel,
            'is_sent' => $this->is_sent,
            'created_at' => $this->created_at->toIso8601String(),
            'card' => [
                'id' => $this->card->id,
                'title' => $this->card->title,
                // Add other card fields as needed
            ],
        ];
    }
}
