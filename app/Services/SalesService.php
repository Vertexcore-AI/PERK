<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Batch;
use App\Models\InventoryStock;
use App\Models\SerialItem;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SalesService
{
    public function __construct(
        private InventoryService $inventoryService,
        private BatchService $batchService
    ) {}

    /**
     * Process complete sale transaction
     */
    public function processSale(array $saleData): Sale
    {
        return DB::transaction(function () use ($saleData) {
            // 1. Validate stock availability first
            $this->validateStockAvailability($saleData['items']);

            // 2. Create sale record
            $sale = $this->createSale($saleData);

            // 3. Process each sale item with batch selection
            foreach ($saleData['items'] as $itemData) {
                $this->processSaleItem($sale, $itemData);
            }

            // 4. Update sale totals
            $this->updateSaleTotals($sale);

            // 5. Log transaction
            $this->logSaleTransaction($sale);

            return $sale->load(['saleItems.item', 'saleItems.batch', 'customer']);
        });
    }

    /**
     * FIFO Batch Selection Logic
     */
    public function selectBatchesFIFO(int $itemId, int $requestedQuantity, ?int $preferredBatchId = null): array
    {
        $selectedBatches = [];
        $remainingQuantity = $requestedQuantity;

        // If preferred batch specified, try it first
        if ($preferredBatchId) {
            $preferredBatch = $this->getAvailableBatch($preferredBatchId, $itemId);
            if ($preferredBatch && $preferredBatch->available_quantity > 0) {
                $takeQuantity = min($remainingQuantity, $preferredBatch->available_quantity);
                $selectedBatches[] = [
                    'batch' => $preferredBatch,
                    'quantity' => $takeQuantity,
                    'unit_price' => $preferredBatch->selling_price,
                    'unit_cost' => $preferredBatch->unit_cost
                ];
                $remainingQuantity -= $takeQuantity;
            }
        }

        // FIFO selection for remaining quantity
        if ($remainingQuantity > 0) {
            $availableBatches = $this->getAvailableBatchesFIFO($itemId, $preferredBatchId);

            foreach ($availableBatches as $batch) {
                if ($remainingQuantity <= 0) break;

                $takeQuantity = min($remainingQuantity, $batch->available_quantity);
                $selectedBatches[] = [
                    'batch' => $batch,
                    'quantity' => $takeQuantity,
                    'unit_price' => $batch->selling_price,
                    'unit_cost' => $batch->unit_cost
                ];
                $remainingQuantity -= $takeQuantity;
            }
        }

        if ($remainingQuantity > 0) {
            throw new Exception("Insufficient stock for item ID {$itemId}. Missing quantity: {$remainingQuantity}");
        }

        return $selectedBatches;
    }

    /**
     * Validate stock availability before sale
     */
    public function validateStockAvailability(array $items): array
    {
        $validationResults = [];

        foreach ($items as $item) {
            $itemId = $item['item_id'];
            $requestedQty = $item['quantity'];

            $availableStock = $this->inventoryService->getTotalAvailableStock($itemId);

            $validationResults[$itemId] = [
                'requested' => $requestedQty,
                'available' => $availableStock,
                'sufficient' => $availableStock >= $requestedQty,
                'shortage' => $availableStock < $requestedQty ? $requestedQty - $availableStock : 0
            ];

            // Throw exception if insufficient stock
            if ($availableStock < $requestedQty) {
                $item = Item::find($itemId);
                throw new Exception("Insufficient stock for item '{$item->item_no}'. Available: {$availableStock}, Requested: {$requestedQty}");
            }
        }

        return $validationResults;
    }

    /**
     * Calculate sale totals with discounts and taxes
     */
    public function calculateSaleTotal(array $items): array
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $totalVat = 0;
        $total = 0;

        foreach ($items as $item) {
            $itemSubtotal = $item['unit_price'] * $item['quantity'];
            $itemDiscount = $itemSubtotal * ($item['discount'] / 100);
            $itemAfterDiscount = $itemSubtotal - $itemDiscount;
            $itemVat = $itemAfterDiscount * ($item['vat'] / 100);
            $itemTotal = $itemAfterDiscount + $itemVat;

            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;
            $totalVat += $itemVat;
            $total += $itemTotal;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'total_vat' => round($totalVat, 2),
            'total' => round($total, 2)
        ];
    }

    /**
     * Get available batches for an item with FIFO ordering
     */
    public function getAvailableBatchesForItem(int $itemId): array
    {
        $batches = Batch::where('item_id', $itemId)
            ->where('remaining_qty', '>', 0)
            ->orderBy('received_date', 'asc') // FIFO
            ->orderBy('id', 'asc')
            ->get();

        return $batches->map(function ($batch) {
            return [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_no,
                'available_quantity' => $batch->remaining_qty,
                'selling_price' => $batch->selling_price,
                'unit_cost' => $batch->unit_cost,
                'expiry_date' => $batch->expiry_date,
                'received_date' => $batch->received_date,
            ];
        })->toArray();
    }

    /**
     * Preview batch selection without processing
     */
    public function previewBatchSelection(int $itemId, int $quantity, ?int $preferredBatchId = null): array
    {
        try {
            $selectedBatches = $this->selectBatchesFIFO($itemId, $quantity, $preferredBatchId);

            return [
                'success' => true,
                'batches' => $selectedBatches,
                'total_cost' => array_sum(array_map(fn($b) => $b['unit_cost'] * $b['quantity'], $selectedBatches)),
                'total_price' => array_sum(array_map(fn($b) => $b['unit_price'] * $b['quantity'], $selectedBatches)),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'available_stock' => $this->inventoryService->getTotalAvailableStock($itemId),
            ];
        }
    }

    // Private helper methods

    private function createSale(array $saleData): Sale
    {
        return Sale::create([
            'customer_id' => $saleData['customer_id'],
            'sale_date' => $saleData['sale_date'] ?? now()->toDateString(),
            'total_amount' => 0, // Will be calculated later
            'payment_method' => $saleData['payment_method'] ?? 'cash',
            'cash_amount' => $saleData['cash_amount'] ?? 0,
            'card_amount' => $saleData['card_amount'] ?? 0,
            'status' => 'completed',
            'notes' => $saleData['notes'] ?? null,
            'discount_percentage' => $saleData['discount_percentage'] ?? 0,
            'discount_amount' => 0, // Will be calculated later
            'vat_percentage' => $saleData['vat_percentage'] ?? 0,
            'vat_amount' => 0, // Will be calculated later
            'subtotal' => 0, // Will be calculated later
        ]);
    }

    private function processSaleItem(Sale $sale, array $itemData): void
    {
        $selectedBatches = $this->selectBatchesFIFO(
            $itemData['item_id'],
            $itemData['quantity'],
            $itemData['batch_id'] ?? null
        );

        foreach ($selectedBatches as $batchSelection) {
            $batch = $batchSelection['batch'];
            $quantity = $batchSelection['quantity'];
            $unitPrice = $batchSelection['unit_price'];
            $unitCost = $batchSelection['unit_cost'];

            // Calculate item total (no discount/VAT here - done at bill level)
            $itemTotal = $unitPrice * $quantity;

            // Create sale item record
            $saleItem = SaleItem::create([
                'sale_id' => $sale->sale_id,
                'item_id' => $itemData['item_id'],
                'batch_id' => $batch->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'total' => $itemTotal,
            ]);

            // Deduct stock from inventory
            $this->deductStock($saleItem);
        }
    }

    private function deductStock(SaleItem $saleItem): void
    {
        // Update batch remaining quantity
        $batch = $saleItem->batch;
        $batch->remaining_qty -= $saleItem->quantity;
        $batch->save();

        // Update inventory stock
        $this->inventoryService->deductStockFromBatch(
            $saleItem->batch_id,
            $saleItem->quantity
        );

        // Handle serialized items
        if ($saleItem->item->is_serialized) {
            $this->updateSerialItemsStatus($saleItem->batch_id, $saleItem->quantity);
        }
    }

    private function updateSaleTotals(Sale $sale): void
    {
        // Calculate subtotal from all sale items
        $subtotal = $sale->saleItems()->sum('total');

        // Calculate discount amount
        $discountAmount = $subtotal * ($sale->discount_percentage / 100);
        $afterDiscount = $subtotal - $discountAmount;

        // Calculate VAT amount
        $vatAmount = $afterDiscount * ($sale->vat_percentage / 100);

        // Calculate final total
        $totalAmount = $afterDiscount + $vatAmount;

        $sale->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount
        ]);
    }

    private function getAvailableBatch(int $batchId, int $itemId): ?Batch
    {
        return Batch::where('batch_id', $batchId)
            ->where('item_id', $itemId)
            ->where('remaining_quantity', '>', 0)
            ->first();
    }

    private function getAvailableBatchesFIFO(int $itemId, ?int $excludeBatchId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Batch::where('item_id', $itemId)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('received_date', 'asc')
            ->orderBy('batch_id', 'asc');

        if ($excludeBatchId) {
            $query->where('batch_id', '!=', $excludeBatchId);
        }

        return $query->get()->map(function ($batch) {
            $batch->available_quantity = $batch->remaining_quantity;
            return $batch;
        });
    }

    private function updateSerialItemsStatus(int $batchId, int $quantity): void
    {
        SerialItem::where('batch_id', $batchId)
            ->where('status', 0) // Available
            ->take($quantity)
            ->update(['status' => 1]); // Sold
    }

    private function logSaleTransaction(Sale $sale): void
    {
        Log::info('Sale processed successfully', [
            'sale_id' => $sale->sale_id,
            'customer_id' => $sale->customer_id,
            'total_amount' => $sale->total_amount,
            'items_count' => $sale->saleItems()->count(),
        ]);
    }
}