<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request, Client $client, DocumentNumberService $numbers): InvoiceResource
    {
        Gate::authorize('update', $client);

        $invoice = DB::transaction(function () use ($request, $client, $numbers) {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $data['number'] = $numbers->nextInvoiceNumber();
            $invoice = $client->invoices()->create($data);
            $invoice->items()->createMany($items);

            return $invoice;
        });
        return new InvoiceResource($invoice->load('items'));
    }
    public function show(Invoice $invoice): InvoiceResource
    {
        Gate::authorize('view', $invoice->client);
        return new InvoiceResource($invoice->load('items'));
    }
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): InvoiceResource
    {
        Gate::authorize('update', $invoice->client);
        DB::transaction(function () use ($request, $invoice) {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            $invoice->update($data);
            $invoice->items()->delete();
            $invoice->items()->createMany($items);
        });
        return new InvoiceResource($invoice->refresh()->load('items'));
    }
}
