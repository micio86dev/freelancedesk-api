<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'category' => $this->category,
            'amount' => $this->amount,
            'spentAt' => $this->spent_at->toDateString()
        ];
    }
}
