<?php

namespace App\Http\Requests\Project;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }
}
