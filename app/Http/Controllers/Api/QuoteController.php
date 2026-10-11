<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quote\StoreQuoteRequest;
use App\Http\Requests\Quote\UpdateQuoteRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Client;
use App\Models\Quote;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class QuoteController extends Controller
{
    public function store(StoreQuoteRequest $request, Client $client, DocumentNumberService $numbers): QuoteResource
    {
        Gate::authorize('update', $client);

        $quote = DB::transaction(function () use ($request, $client, $numbers) {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $data['number'] = $numbers->nextQuoteNumber();
            $quote = $client->quotes()->create($data);
            $quote->items()->createMany($items);

            return $quote;
        });

        return new QuoteResource($quote->load('items'));
    }

    public function show(Quote $quote): QuoteResource
    {
        Gate::authorize('view', $quote->client);

        return new QuoteResource($quote->load('items'));
    }

    public function update(UpdateQuoteRequest $request, Quote $quote): QuoteResource
    {
        Gate::authorize('update', $quote->client);

        DB::transaction(function () use ($request, $quote) {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $quote->update($data);
            $quote->items()->delete();
            $quote->items()->createMany($items);
        });

        return new QuoteResource($quote->refresh()->load('items'));
    }
}
