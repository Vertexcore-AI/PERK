<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuoteItem;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuotationService
{
    public function createQuotation(array $data): Quotation
    {
        \Log::info('QuotationService::createQuotation called with data:', $data);

        return DB::transaction(function () use ($data) {
            \Log::info('Creating quotation with data:', [
                'customer_id' => $data['customer_id'],
                'validity_days' => $data['validity_days'] ?? 30,
                'items_count' => count($data['items'])
            ]);

            $quotation = Quotation::create([
                'customer_id' => $data['customer_id'],
                'quote_date' => Carbon::now()->toDateString(),
                'valid_until' => Carbon::now()->addDays((int)($data['validity_days'] ?? 30))->toDateString(),
                'status' => 'Pending',
                'car_model' => $data['car_model'] ?? null,
                'car_registration_number' => $data['car_registration_number'] ?? null,
                'manual_customer_name' => $data['manual_customer_name'] ?? null,
                'manual_customer_address' => $data['manual_customer_address'] ?? null
            ]);

            \Log::info('Quotation created:', ['quote_id' => $quotation->quote_id]);

            $totalEstimate = 0;

            foreach ($data['items'] as $index => $itemData) {
                \Log::info("Processing item {$index}:", $itemData);

                $total = $this->calculateItemTotal($itemData);
                \Log::info("Calculated total for item {$index}:", ['total' => $total]);

                $quoteItem = QuoteItem::create([
                    'quote_id' => $quotation->quote_id,
                    'item_id' => $itemData['item_id'],
                    'batch_id' => $itemData['batch_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'vat' => $itemData['vat'] ?? 0,
                    'total' => $total
                ]);

                \Log::info("QuoteItem created:", ['quote_item_id' => $quoteItem->quote_item_id]);

                $totalEstimate += $total;
            }

            \Log::info('Total estimate calculated:', ['total_estimate' => $totalEstimate]);

            $quotation->update(['total_estimate' => $totalEstimate]);

            return $quotation->fresh(['quoteItems.item', 'quoteItems.batch', 'customer']);
        });
    }

    private function calculateItemTotal(array $itemData): float
    {
        $subtotal = $itemData['quantity'] * $itemData['unit_price'];
        $afterDiscount = $subtotal - ($subtotal * ($itemData['discount'] ?? 0) / 100);
        $withVat = $afterDiscount + ($afterDiscount * ($itemData['vat'] ?? 0) / 100);
        return round($withVat, 2);
    }

    public function loadQuotationForPOS(int $quoteId): array
    {
        $quotation = Quotation::with(['quoteItems.item', 'quoteItems.batch', 'customer'])
            ->findOrFail($quoteId);

        if (!$quotation->canConvert()) {
            throw new \Exception('This quotation cannot be converted to a sale.');
        }

        $items = [];

        foreach ($quotation->quoteItems as $quoteItem) {
            // Handle items without batch (no stock items)
            if (!$quoteItem->batch_id) {
                $itemData = [
                    'quote_item_id' => $quoteItem->quote_item_id,
                    'item_id' => $quoteItem->item_id,
                    'item' => $quoteItem->item,
                    'original_batch_id' => null,
                    'original_batch' => null,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->unit_price,
                    'discount' => $quoteItem->discount,
                    'vat' => $quoteItem->vat,
                    'status' => 'no_batch',
                    'alternatives' => $this->getAlternativeBatches(
                        $quoteItem->item_id,
                        $quoteItem->quantity
                    )
                ];
            } else {
                $stockStatus = $this->checkBatchStock($quoteItem->batch_id, $quoteItem->quantity);

                $itemData = [
                    'quote_item_id' => $quoteItem->quote_item_id,
                    'item_id' => $quoteItem->item_id,
                    'item' => $quoteItem->item,
                    'original_batch_id' => $quoteItem->batch_id,
                    'original_batch' => $quoteItem->batch,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->unit_price,
                    'discount' => $quoteItem->discount,
                    'vat' => $quoteItem->vat,
                    'status' => $stockStatus['available'] ? 'available' : 'out_of_stock'
                ];

                if (!$stockStatus['available']) {
                    $itemData['alternatives'] = $this->getAlternativeBatches(
                        $quoteItem->item_id,
                        $quoteItem->quantity
                    );
                }
            }

            $items[] = $itemData;
        }

        return [
            'quotation' => $quotation,
            'items' => $items
        ];
    }

    private function checkBatchStock(int $batchId, int $requiredQuantity): array
    {
        $batch = Batch::find($batchId);

        if (!$batch) {
            return ['available' => false, 'stock' => 0];
        }

        return [
            'available' => $batch->remaining_qty >= $requiredQuantity,
            'stock' => $batch->remaining_qty
        ];
    }

    public function getAlternativeBatches(int $itemId, int $requiredQuantity): array
    {
        return Batch::where('item_id', $itemId)
            ->where('remaining_qty', '>=', $requiredQuantity)
            ->where('remaining_qty', '>', 0)
            ->select('id as batch_id', 'batch_no as batch_number', 'remaining_qty as remaining_quantity', 'selling_price', 'expiry_date')
            ->orderBy('created_at', 'asc') // FIFO
            ->get()
            ->toArray();
    }

    public function convertToSale(int $quoteId, array $finalItems): Sale
    {
        return DB::transaction(function () use ($quoteId, $finalItems) {
            $quotation = Quotation::findOrFail($quoteId);

            if (!$quotation->canConvert()) {
                throw new \Exception('This quotation cannot be converted to a sale.');
            }

            // Create sale
            $sale = Sale::create([
                'customer_id' => $this->getOrCreateCustomerId($quotation),
                'sale_date' => Carbon::now()->toDateString(),
                'status' => 'Completed',
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            // Create sale items
            foreach ($finalItems as $item) {
                $batch = Batch::findOrFail($item['batch_id']);

                // Check stock again
                if ($batch->remaining_qty < $item['quantity']) {
                    throw new \Exception("Insufficient stock for item {$item['item_id']}");
                }

                $total = $this->calculateItemTotal($item);

                SaleItem::create([
                    'sale_id' => $sale->sale_id,
                    'item_id' => $item['item_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost' => $batch->unit_cost,
                    'discount' => $item['discount'] ?? 0,
                    'vat' => $item['vat'] ?? 0,
                    'total' => $total
                ]);

                // Deduct stock
                $batch->decrement('remaining_qty', $item['quantity']);

                // Update inventory stock
                DB::table('inventory_stock')
                    ->where('batch_id', $item['batch_id'])
                    ->decrement('quantity', $item['quantity']);

                $totalAmount += $total;
            }

            $sale->update(['total_amount' => $totalAmount]);

            // Mark quotation as converted
            $quotation->markAsConverted();

            return $sale->fresh(['saleItems', 'customer']);
        });
    }

    public function updateExpiredQuotes(): int
    {
        $expiredCount = Quotation::where('status', 'Pending')
            ->where('valid_until', '<', Carbon::now()->toDateString())
            ->update(['status' => 'Expired']);

        return $expiredCount;
    }

    public function getPendingQuotations()
    {
        return Quotation::with(['quoteItems'])
            ->where('status', 'Pending')
            ->where('valid_until', '>=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($quotation) {
                // Add customer name (either from relationship or manual)
                $quotation->customer_name = $quotation->customer
                    ? $quotation->customer->name
                    : ($quotation->manual_customer_name ?? 'Unknown Customer');
                return $quotation;
            });
    }

    public function duplicateQuotation(int $quoteId): Quotation
    {
        $originalQuote = Quotation::with('quoteItems')->findOrFail($quoteId);

        return DB::transaction(function () use ($originalQuote) {
            $newQuote = Quotation::create([
                'customer_id' => $originalQuote->customer_id,
                'quote_date' => Carbon::now()->toDateString(),
                'valid_until' => Carbon::now()->addDays(30)->toDateString(),
                'total_estimate' => $originalQuote->total_estimate,
                'status' => 'Pending'
            ]);

            foreach ($originalQuote->quoteItems as $item) {
                QuoteItem::create([
                    'quote_id' => $newQuote->quote_id,
                    'item_id' => $item->item_id,
                    'batch_id' => $item->batch_id ?? null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'vat' => $item->vat,
                    'total' => $item->total
                ]);
            }

            return $newQuote->fresh(['quoteItems', 'customer']);
        });
    }

    /**
     * Get or create customer ID from quotation
     */
    private function getOrCreateCustomerId(Quotation $quotation): int
    {
        // If quotation already has a customer_id, use it
        if ($quotation->customer_id) {
            return $quotation->customer_id;
        }

        // If manual customer data exists, create a customer record
        if ($quotation->manual_customer_name) {
            $customer = Customer::create([
                'name' => $quotation->manual_customer_name,
                'address' => $quotation->manual_customer_address ?? '',
                'type' => 'Retail', // Default type for converted customers
                'contact' => '', // Default empty contact
            ]);
            return $customer->id;
        }

        throw new \Exception('No customer information available for this quotation.');
    }
}