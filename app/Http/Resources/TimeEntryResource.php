<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'taskId' => $this->task_id,
            'description' => $this->description,
            'startedAt' => $this->started_at?->toISOString(),
            'endedAt' => $this->ended_at?->toISOString(),
            'durationSeconds' => $this->duration_seconds
        ];
    }
}
