<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;

class DocumentNumberService
{
    public function nextQuoteNumber(): string
    {
        return $this->next('Q', Quote::class);
    }

    public function nextInvoiceNumber(): string
    {
        return $this->next('INV', Invoice::class);
    }

    private function next(string $prefix, string $model): string
    {
        $year = now()->year;
        $count = $model::query()->where('number', 'like', "prefix-$year-%")->count() + 1;

        return sprintf('%s-%d-%04d', $prefix, $year, $count);
    }
}
