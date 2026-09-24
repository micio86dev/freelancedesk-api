<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Project $project): AnonymousResourceCollection
    {
        Gate::authorize('view', $project);
        return TaskResource::collection($project->tasks()->latest()->get());
    }

    public function store(StoreTaskRequest $request, Project $project): TaskResource
    {
        Gate::authorize('update', $project);
        return new TaskResource($project->tasks()->create($request->validated()));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        Gate::authorize('update', $task->project);
        $task->update($request->validated());
        return new TaskResource($task->refresh());
    }

    public function destroy(Task $task): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('update', $task->project);
        $task->delete();
        return response()->json(null, 204);
    }
}
