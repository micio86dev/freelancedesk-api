<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Expense;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;

class DashboardService
{
    public function forUser(User $user): array
    {
        $projectIds = Project::ownedBy($user)->pluck('id');
        $seconds = (int) TimeEntry::query()->whereIn('project_id', $projectIds)
            ->whereBetween('started_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('duration_seconds');
        $expenses = (float) Expense::query()
            ->whereIn('project_id', $projectIds)
            ->whereBetween('spent_at', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('amount');

        $potentialRevenue = Project::ownedBy($user)->get()->sum(function (Project $project) {
            $seconds = (int) $project->timeEntries()->sum('duration_seconds');
            return ($seconds / 3600) * (float) ($project->hourly_rate ?? 0);
        });

        return [
            'clients' => $user->clients()->count(),
            'activeProjects' => Project::ownedBy($user)->where('status', ProjectStatus::Active)->count(),
            'workedHoursThisMonth' => round($seconds / 3600, 2),
            'expensesThisMonth' => round($expenses, 2),
            'potentialRevenue' => round($potentialRevenue, 2),
        ];
    }
}
