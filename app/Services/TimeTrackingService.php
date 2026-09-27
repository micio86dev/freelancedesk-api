<?php

namespace App\Services;

use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Validation\ValidationException;

class TimeTrackingService
{
    public function start(Project $project, ?int $taskId, ?string $description): TimeEntry
    {
        if ($project->timeEntries()->whereNull('ended_at')->exists()) {
            throw ValidationException::withMessages(['timer' => ['Esiste già un timer attivo per questo progetto.']]);
        }

        return $project->timeEntries()->create([
            'task_id' => $taskId,
            'description' => $description,
            'started_at' => now(),
        ]);
    }
    public function stop(TimeEntry $entry): TimeEntry
    {
        if ($entry->ended_at !== null) {
            return $entry;
        }
        $endedAt = now();
        $entry->update([
            'ended_at' => $endedAt,
            'duration_seconds' => $entry->started_at->diffInSeconds($endedAt),
        ]);
        return $entry->refresh();
    }
}
