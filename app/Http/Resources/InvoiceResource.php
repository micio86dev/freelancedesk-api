<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('items');

        return [
            'id' => $this->id,
            'number' => $this->number,
            'status' => $this->status->value,
            'issuedAt' => $this->issued_at?->toDateString(),
            'dueAt' => $this->due_at?->toDateString(),
            'notes' => $this->notes,
            'items' => $items,
            'total' => $this->relationLoaded('items') ?
                round($this->items->sum(fn($item) => (float)$item->quantity * (float)$item->unit_price), 2) : null,
        ];
    }
}
