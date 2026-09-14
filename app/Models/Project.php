<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'hourly_rate',
        'started_at',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'hourly_rate' => 'decimal:2',
            'started_at' => 'date',
            'deadline' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'client',
            fn(Builder $query) => $query->where('user_id', $user->id),
        );
    }

    public function scopeStatus(Builder $query, ?ProjectStatus $status): Builder
    {
        return $query->when(
            $status,
            fn(Builder $query) => $query->where('status', $status),
        );
    }
}
