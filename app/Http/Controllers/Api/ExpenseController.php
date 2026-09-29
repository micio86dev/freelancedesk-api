<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExpenseController extends Controller
{
    public function index(Project $project): AnonymousResourceCollection
    {
        Gate::authorize('view', $project);
        return ExpenseResource::collection($project->expenses()->latest('spent_at')->get());
    }

    public function store(StoreExpenseRequest $request, Project $project): ExpenseResource
    {
        Gate::authorize('update', $project);
        return new ExpenseResource($project->expenses()->create($request->validated()));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): ExpenseResource
    {
        Gate::authorize('update', $expense->project);
        $expense->update($request->validated());
        return new ExpenseResource($expense->refresh());
    }

    public function destroy(Expense $expense): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('update', $expense->project);
        $expense->delete();
        return response()->json(null, 204);
    }
}
