<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeEntry\StartTimeEntryRequest;
use App\Http\Resources\TimeEntryResource;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Services\TimeTrackingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TimeEntryController extends Controller
{
    public function __construct(private readonly TimeTrackingService $service) {}

    public function index(Project $project): AnonymousResourceCollection
    {
        Gate::authorize('view', $project);
        return TimeEntryResource::collection($project->timeEntries()->latest('started_at')->get());
    }

    public function start(StartTimeEntryRequest $request, Project $project): TimeEntryResource
    {
        Gate::authorize('update', $project);
        if ($request->filled('task_id') && ! $project->tasks()->whereKey($request->integer('task_id'))->exists()) {
            abort(422, 'Il task non appartiene al progetto.');
        }
        $entry = $this->service->start($project, $request->integer('task_id') ?: null, $request->string('description')->toString() ?: null);
        return new TimeEntryResource($entry);
    }

    public function stop(TimeEntry $timeEntry): TimeEntryResource
    {
        Gate::authorize('update', $timeEntry->project);
        return new TimeEntryResource($this->service->stop($timeEntry));
    }
}
