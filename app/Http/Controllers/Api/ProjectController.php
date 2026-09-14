<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = ProjectStatus::tryFrom($request->string('status')->toString());
        $projects = Project::query()->ownedBy($request->user())->status($status)->with('client')->latest()->paginate(20);
        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request, Client $client): ProjectResource
    {
        Gate::authorize('update', $client);
        $project = $client->projects()->create($request->validated());
        return new ProjectResource($project->load('client'));
    }

    public function show(Project $project): ProjectResource
    {
        Gate::authorize('view', $project);
        return new ProjectResource($project->load('client'));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        Gate::authorize('update', $project);
        $project->update($request->validated());
        return new ProjectResource($project->refresh()->load('client'));
    }

    public function destroy(Project $project): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('delete', $project);
        $project->delete();
        return response()->json(null, 204);
    }
}
