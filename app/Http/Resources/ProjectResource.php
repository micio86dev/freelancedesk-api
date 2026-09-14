<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->value,
            'hourlyRate' => $this->hourly_rate,
            'startedAt' => $this->started_at?->toDateString(),
            'deadline' => $this->deadline?->toDateString(),
            'client' => new ClientResource($this->whenLoaded('client')),
        ];
    }
}
